<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\InstitutionalServiceResource;
use App\Http\Resources\Api\V1\ServiceCategoryResource;
use App\Models\InstitutionalService;
use App\Models\ServiceCategory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CatalogController extends Controller
{
    public function categories(): AnonymousResourceCollection
    {
        $categories = ServiceCategory::query()
            ->where('is_active', true)
            ->withCount('institutionalServices')
            ->orderBy('name')
            ->get();

        return ServiceCategoryResource::collection($categories);
    }

    public function category(ServiceCategory $serviceCategory): ServiceCategoryResource
    {
        abort_unless($serviceCategory->is_active, 404);

        $serviceCategory->loadCount('institutionalServices');

        return new ServiceCategoryResource($serviceCategory);
    }

    public function services(): AnonymousResourceCollection
    {
        $request = request();
        $query = InstitutionalService::query()
            ->where('is_active', true)
            ->with('serviceCategory:id,name');

        if ($request->filled('search')) {
            $term = trim($request->string('search')->value());
            $query->where(function ($builder) use ($term): void {
                $builder->where('name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('service_category_id', (int) $request->integer('category'));
        }

        if ($request->filled('requires_approval')) {
            $query->where('requires_approval', filter_var($request->boolean('requires_approval'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('duration_min')) {
            $query->where('duration_minutes', '>=', (int) $request->integer('duration_min'));
        }

        if ($request->filled('duration_max')) {
            $query->where('duration_minutes', '<=', (int) $request->integer('duration_max'));
        }

        $perPage = min(50, max(1, (int) $request->integer('per_page', 15)));

        return InstitutionalServiceResource::collection(
            $query->orderBy('name')->paginate($perPage)
        );
    }

    public function service(InstitutionalService $institutionalService): InstitutionalServiceResource
    {
        abort_unless($institutionalService->is_active, 404);

        $institutionalService->load('serviceCategory:id,name');

        return new InstitutionalServiceResource($institutionalService);
    }
}
