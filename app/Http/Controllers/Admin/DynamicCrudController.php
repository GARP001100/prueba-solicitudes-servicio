<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DynamicCrudService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DynamicCrudController extends Controller
{
    public function __construct(private readonly DynamicCrudService $crud) {}

    public function home(): RedirectResponse
    {
        $first = $this->crud->availableResources()[0]['resource'] ?? null;
        abort_unless($first, 404);

        return redirect()->route('admin.crud.index', ['resource' => $first]);
    }

    public function index(string $resource): View
    {
        return view('admin.crud', [
            'metadata' => $this->crud->metadata($resource),
        ]);
    }

    public function data(Request $request, string $resource): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => array_merge(
                $this->crud->metadata($resource),
                $this->crud->paginate(
                    $resource,
                    (string) $request->query('search', ''),
                    max(1, $request->integer('page', 1)),
                ),
            ),
        ]);
    }

    public function show(string $resource, int $id): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => $this->crud->find($resource, $id),
        ]);
    }

    public function store(Request $request, string $resource): JsonResponse
    {
        $record = $this->crud->create($resource, $request->all());

        return response()->json([
            'status' => true,
            'message' => 'Registro creado correctamente.',
            'id' => $record->getKey(),
        ], 201);
    }

    public function update(Request $request, string $resource, int $id): JsonResponse
    {
        $this->crud->update($resource, $id, $request->all());

        return response()->json([
            'status' => true,
            'message' => 'Registro actualizado correctamente.',
        ]);
    }

    public function destroy(Request $request, string $resource, int $id): JsonResponse
    {
        $this->crud->delete($resource, $id, (int) $request->user()->getKey());

        return response()->json([
            'status' => true,
            'message' => 'Registro eliminado correctamente.',
        ]);
    }
}
