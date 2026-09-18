<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DynamicCrudService
{
    public function resources(): array
    {
        return config('crud.resources', []);
    }

    public function definition(string $resource): array
    {
        $configured = $this->resources()[$resource] ?? null;

        if (is_array($configured) && isset($configured['model'])) {
            abort_unless(is_subclass_of($configured['model'], Model::class), 404);

            return $configured;
        }

        $class = 'App\\Models\\'.Str::studly(Str::singular($resource));

        abort_unless(
            class_exists($class)
                && is_subclass_of($class, Model::class)
                && method_exists($class, 'crudEnabled')
                && $class::crudEnabled(),
            404,
        );

        return [
            'model' => $class,
            'title' => Str::headline($resource),
            'singular' => Str::lower(Str::headline(Str::singular($resource))),
            'icon' => 'database',
        ];
    }

    public function model(string $resource): Model
    {
        $class = $this->definition($resource)['model'];

        return new $class;
    }

    public function availableResources(): array
    {
        $resources = collect($this->resources())
            ->mapWithKeys(fn (array $definition, string $key): array => [
                $key => [
                    'resource' => $key,
                    'title' => $definition['title'] ?? Str::headline($key),
                    'singular' => $definition['singular'] ?? Str::lower(Str::headline(Str::singular($key))),
                    'icon' => $definition['icon'] ?? 'database',
                ],
            ]);

        foreach (File::allFiles(app_path('Models')) as $file) {
            $relative = Str::of($file->getRelativePathname())
                ->replace('\\', '/')
                ->beforeLast('.php')
                ->replace('/', '\\');
            $class = 'App\\Models\\'.$relative;

            if (! class_exists($class) || ! is_subclass_of($class, Model::class)) {
                continue;
            }

            if (! method_exists($class, 'crudEnabled') || ! $class::crudEnabled()) {
                continue;
            }

            $model = new $class;
            $key = method_exists($model, 'crudResource')
                ? $model->crudResource()
                : Str::plural(Str::snake(class_basename($class), '-'));

            $resources->put($key, [
                'resource' => $key,
                'title' => method_exists($model, 'crudTitle') ? $model->crudTitle() : Str::headline($key),
                'singular' => method_exists($model, 'crudSingular')
                    ? $model->crudSingular()
                    : Str::lower(Str::headline(Str::singular($key))),
                'icon' => method_exists($model, 'crudIcon') ? $model->crudIcon() : 'database',
            ]);
        }

        return $resources->values()->all();
    }

    public function metadata(string $resource, ?int $id = null): array
    {
        $model = $this->model($resource);
        $labels = $this->labels($resource);
        $rules = $this->rules($resource, $id);

        $fields = collect($this->fields($resource))
            ->reject(fn (string $field): bool => in_array($field, $this->hidden($resource), true))
            ->map(function (string $field) use ($model, $labels, $rules): array {
                $options = $this->options($model, $field);
                $type = $this->fieldType($model, $field, $options);

                return [
                    'name' => $field,
                    'label' => $labels[$field] ?? Str::headline($field),
                    'type' => $type,
                    'required' => in_array('required', Arr::wrap($rules[$field] ?? []), true),
                    'options' => $options,
                    'multiple' => $type === 'multiselect',
                ];
            })
            ->values()
            ->all();

        return [
            'resource' => $resource,
            'title' => $this->title($resource),
            'singular' => $this->singular($resource),
            'icon' => $this->icon($resource),
            'fields' => $fields,
            'listFields' => $this->listFields($resource),
            'labels' => $labels,
            'resources' => $this->availableResources(),
        ];
    }

    public function paginate(string $resource, string $search = '', int $page = 1): array
    {
        $model = $this->model($resource);
        $query = $model->newQuery();
        $searchable = $this->searchable($resource);

        if (trim($search) !== '' && $searchable !== []) {
            $query->where(function ($builder) use ($searchable, $search): void {
                foreach ($searchable as $index => $field) {
                    $method = $index === 0 ? 'where' : 'orWhere';
                    $builder->{$method}($field, 'like', '%'.trim($search).'%');
                }
            });
        }

        $paginator = $query
            ->orderByDesc($model->getKeyName())
            ->paginate(
                perPage: (int) config('crud.per_page', 10),
                page: max(1, $page),
            );

        return [
            'records' => collect($paginator->items())
                ->map(fn (Model $record): array => $this->row(
                    resource: $resource,
                    record: $record,
                    fields: $this->listFields($resource),
                ))
                ->values()
                ->all(),
            'pagination' => $this->pagination($paginator),
        ];
    }

    public function find(string $resource, int $id): array
    {
        $record = $this->model($resource)->newQuery()->findOrFail($id);
        $data = [];

        foreach ($this->fields($resource) as $field) {
            $valueMethod = 'get'.Str::studly($field).'Value';

            if (method_exists($record, $valueMethod)) {
                $data[$field] = $record->{$valueMethod}();

                continue;
            }

            $data[$field] = match (true) {
                $field === 'password' => '',
                $record instanceof User && $field === 'role' => $record->getRoleNames()->first(),
                $record instanceof Role && $field === 'permissions' => $record->permissions()->pluck('name')->all(),
                default => $record->getAttribute($field),
            };
        }

        return ['id' => $record->getKey(), 'data' => $data];
    }

    public function create(string $resource, array $payload): Model
    {
        $model = $this->model($resource);
        $data = $this->validated($resource, $payload);

        return $model->getConnection()->transaction(function () use ($resource, $model, $data): Model {
            $virtual = $this->extractVirtual($resource, $data);
            $data = array_merge($this->defaults($resource), $data);
            $data = $this->preparePassword($data);

            $record = $model->newQuery()->create($data);
            $this->saveVirtual($record, $virtual);

            return $record->refresh();
        });
    }

    public function update(string $resource, int $id, array $payload): Model
    {
        $model = $this->model($resource);
        $record = $model->newQuery()->findOrFail($id);
        $data = $this->validated($resource, $payload, $id);

        return $model->getConnection()->transaction(function () use ($resource, $record, $data): Model {
            $virtual = $this->extractVirtual($resource, $data);
            $data = array_merge($this->defaults($resource), $data);
            $data = $this->preparePassword($data, true);

            $record->fill($data)->save();
            $this->saveVirtual($record, $virtual);

            return $record->refresh();
        });
    }

    public function delete(string $resource, int $id, int $authenticatedId): void
    {
        $record = $this->model($resource)->newQuery()->findOrFail($id);
        $definition = $this->definition($resource);

        if ($record instanceof User && (int) $record->getKey() === $authenticatedId) {
            throw ValidationException::withMessages([
                'record' => 'No puedes eliminar tu propia cuenta desde este módulo.',
            ]);
        }

        if ($record instanceof User && $record->hasRole('admin') && User::role('admin')->count() <= 1) {
            throw ValidationException::withMessages([
                'record' => 'No se puede eliminar el último administrador.',
            ]);
        }

        if (in_array((string) $record->getAttribute('name'), $definition['protected_names'] ?? [], true)) {
            throw ValidationException::withMessages([
                'record' => 'Este registro está protegido y no puede eliminarse.',
            ]);
        }

        $record->delete();
    }

    public function fields(string $resource): array
    {
        $model = $this->model($resource);
        $definition = $this->definition($resource);

        return method_exists($model, 'crudFields')
            ? $model->crudFields()
            : ($definition['fields'] ?? $model->getFillable());
    }

    public function listFields(string $resource): array
    {
        $model = $this->model($resource);
        $definition = $this->definition($resource);

        if (method_exists($model, 'crudListFields')) {
            return $model->crudListFields();
        }

        return $definition['list_fields'] ?? array_values(array_unique([
            $model->getKeyName(),
            ...array_values(array_diff($this->fields($resource), ['password'])),
            'created_at',
        ]));
    }

    public function labels(string $resource): array
    {
        $model = $this->model($resource);
        $labels = $this->definition($resource)['labels'] ?? [];

        if (method_exists($model, 'crudLabels')) {
            $labels = array_merge($labels, $model->crudLabels());
        }

        foreach (array_unique([...$this->fields($resource), ...$this->listFields($resource)]) as $field) {
            $labels[$field] ??= Str::headline($field);
        }

        return $labels;
    }

    public function searchable(string $resource): array
    {
        $model = $this->model($resource);
        $definition = $this->definition($resource);

        if (method_exists($model, 'crudSearchable')) {
            return $model->crudSearchable();
        }

        if (isset($definition['searchable'])) {
            return $definition['searchable'];
        }

        return collect($this->fields($resource))
            ->reject(fn (string $field): bool => in_array($field, [
                'password',
                'role',
                'permissions',
            ], true))
            ->filter(fn (string $field): bool => Schema::hasColumn($model->getTable(), $field))
            ->values()
            ->all();
    }

    public function hidden(string $resource): array
    {
        $model = $this->model($resource);

        return method_exists($model, 'crudHidden')
            ? $model->crudHidden()
            : ($this->definition($resource)['hidden'] ?? []);
    }

    public function virtualFields(string $resource): array
    {
        $model = $this->model($resource);

        return method_exists($model, 'crudVirtualFields')
            ? $model->crudVirtualFields()
            : ($this->definition($resource)['virtual'] ?? []);
    }

    public function rules(string $resource, ?int $id = null): array
    {
        $model = $this->model($resource);
        $definition = $this->definition($resource);

        if (method_exists($model, 'crudRules')) {
            return $model->crudRules($id);
        }

        if (($definition['rules'] ?? []) !== []) {
            $rules = $definition['rules'];

            if (isset($rules['name']) && in_array($resource, ['roles', 'permissions'], true)) {
                $rules['name'][] = Rule::unique($model->getTable(), 'name')->ignore($id);
            }

            return $rules;
        }

        $rules = [];
        foreach ($this->fields($resource) as $field) {
            $rules[$field] = ['nullable'];
        }

        return $rules;
    }

    public function title(string $resource): string
    {
        $model = $this->model($resource);

        return method_exists($model, 'crudTitle')
            ? $model->crudTitle()
            : ($this->definition($resource)['title'] ?? Str::headline($resource));
    }

    public function singular(string $resource): string
    {
        return $this->definition($resource)['singular']
            ?? Str::lower(Str::headline(Str::singular($resource)));
    }

    public function icon(string $resource): string
    {
        return $this->definition($resource)['icon'] ?? 'database';
    }

    private function validated(string $resource, array $payload, ?int $id = null): array
    {
        $allowed = array_flip($this->fields($resource));
        $payload = array_intersect_key($payload, $allowed);

        return Validator::make($payload, $this->rules($resource, $id))->validate();
    }

    private function options(Model $model, string $field): array
    {
        $selectMethod = 'get'.Str::studly($field).'Select';

        if (method_exists($model, $selectMethod)) {
            return $model->{$selectMethod}();
        }

        $special = match ($field) {
            'role' => Role::query()->where('guard_name', 'web')->orderBy('name')->pluck('name', 'name')->all(),
            'permissions' => Permission::query()->where('guard_name', 'web')->orderBy('name')->pluck('name', 'name')->all(),
            default => [],
        };

        return $special !== [] ? $special : $this->relationOptions($model, $field);
    }

    private function fieldType(Model $model, string $field, array $options): string
    {
        $typeMethod = 'get'.Str::studly($field).'Type';
        if (method_exists($model, $typeMethod)) {
            return (string) $model->{$typeMethod}();
        }

        if ($field === 'permissions') {
            return 'multiselect';
        }

        if (str_ends_with($field, '_id')) {
            $relationName = Str::camel(Str::beforeLast($field, '_id'));
            if (method_exists($model, $relationName)) {
                return 'select';
            }
        }

        if ($options !== []) {
            return 'select';
        }

        return match (true) {
            $field === 'password' => 'password',
            $field === 'email' => 'email',
            str_contains($field, 'date'), str_contains($field, 'fecha') => 'date',
            str_contains($field, 'description'), str_contains($field, 'descripcion'),
            str_contains($field, 'content'), str_contains($field, 'contenido') => 'textarea',
            default => 'text',
        };
    }

    private function relationOptions(Model $model, string $field): array
    {
        if (! str_ends_with($field, '_id')) {
            return [];
        }

        $relationName = Str::camel(Str::beforeLast($field, '_id'));
        if (! method_exists($model, $relationName)) {
            return [];
        }

        $relation = $model->{$relationName}();
        if (! method_exists($relation, 'getRelated')) {
            return [];
        }

        return $relation->getRelated()->newQuery()->get()
            ->mapWithKeys(fn (Model $record): array => [
                $record->getKey() => $this->description($record),
            ])
            ->all();
    }

    private function description(Model $model): string
    {
        if (method_exists($model, 'getDescription')) {
            return (string) $model->getDescription();
        }

        foreach (['name', 'nombre', 'title', 'titulo', 'description', 'descripcion', 'id'] as $field) {
            $value = $model->getAttribute($field);
            if ($value !== null) {
                return (string) $value;
            }
        }

        return (string) $model->getKey();
    }

    private function relationDescription(Model $record, string $field): mixed
    {
        $relationName = Str::camel(Str::beforeLast($field, '_id'));
        if (! method_exists($record, $relationName)) {
            return $record->getAttribute($field);
        }

        $related = $record->{$relationName};

        return $related ? $this->description($related) : null;
    }

    private function row(string $resource, Model $record, array $fields): array
    {
        $row = ['id' => $record->getKey()];

        foreach ($fields as $field) {
            $row[$field] = $this->displayValue($record, $field);
        }

        return $row;
    }

    private function displayValue(Model $record, string $field): mixed
    {
        $valueMethod = 'get'.Str::studly($field).'Value';
        if (method_exists($record, $valueMethod)) {
            return $record->{$valueMethod}();
        }

        return match (true) {
            $record instanceof User && $field === 'role' => $record->getRoleNames()->first() ?? 'Sin rol',
            $record instanceof Role && $field === 'permissions' => $record->permissions()->pluck('name')->implode(', '),
            $record instanceof Permission && $field === 'roles' => $record->roles()->pluck('name')->implode(', '),
            $field === 'created_at' => $record->created_at?->format('d/m/Y H:i'),
            str_ends_with($field, '_id') => $this->relationDescription($record, $field),
            default => $record->getAttribute($field),
        };
    }

    private function extractVirtual(string $resource, array &$data): array
    {
        $virtual = [];

        foreach ($this->virtualFields($resource) as $field) {
            if (array_key_exists($field, $data)) {
                $virtual[$field] = $data[$field];
                unset($data[$field]);
            }
        }

        return $virtual;
    }

    private function saveVirtual(Model $record, array $virtual): void
    {
        if ($record instanceof User && isset($virtual['role'])) {
            $record->syncRoles([$virtual['role']]);
        }

        if ($record instanceof Role && array_key_exists('permissions', $virtual)) {
            $record->syncPermissions(Arr::wrap($virtual['permissions']));
        }

        foreach ($virtual as $field => $value) {
            $method = 'save'.Str::studly($field);
            if (method_exists($record, $method)) {
                $record->{$method}($value);
            }
        }
    }

    private function defaults(string $resource): array
    {
        return $this->definition($resource)['defaults'] ?? [];
    }

    private function preparePassword(array $data, bool $updating = false): array
    {
        if (! array_key_exists('password', $data)) {
            return $data;
        }

        if ($updating && blank($data['password'])) {
            unset($data['password']);

            return $data;
        }

        $data['password'] = Hash::make($data['password']);

        return $data;
    }

    private function pagination(LengthAwarePaginator $paginator): array
    {
        return [
            'currentPage' => $paginator->currentPage(),
            'lastPage' => $paginator->lastPage(),
            'perPage' => $paginator->perPage(),
            'total' => $paginator->total(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
        ];
    }
}
