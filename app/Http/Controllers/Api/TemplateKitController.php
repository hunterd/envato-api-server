<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TemplateKit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TemplateKitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = TemplateKit::query();

        // Filter by category if provided
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $templateKits = $query->paginate($perPage);

        return response()->json($templateKits);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'category' => 'nullable|string|max:255',
                'author' => 'nullable|string|max:255',
                'version' => 'nullable|string|max:50',
                'thumbnail' => 'nullable|string|max:500',
                'tags' => 'nullable|array',
                'files' => 'nullable|array',
                'price' => 'nullable|numeric|min:0',
                'is_active' => 'nullable|boolean',
            ]);

            $templateKit = TemplateKit::create($validated);

            return response()->json([
                'message' => 'Template Kit created successfully',
                'data' => $templateKit,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $templateKit = TemplateKit::find($id);

        if (! $templateKit) {
            return response()->json([
                'message' => 'Template Kit not found',
            ], 404);
        }

        return response()->json([
            'data' => $templateKit,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $templateKit = TemplateKit::find($id);

        if (! $templateKit) {
            return response()->json([
                'message' => 'Template Kit not found',
            ], 404);
        }

        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'category' => 'nullable|string|max:255',
                'author' => 'nullable|string|max:255',
                'version' => 'nullable|string|max:50',
                'thumbnail' => 'nullable|string|max:500',
                'tags' => 'nullable|array',
                'files' => 'nullable|array',
                'price' => 'nullable|numeric|min:0',
                'is_active' => 'nullable|boolean',
            ]);

            $templateKit->update($validated);

            return response()->json([
                'message' => 'Template Kit updated successfully',
                'data' => $templateKit,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $templateKit = TemplateKit::find($id);

        if (! $templateKit) {
            return response()->json([
                'message' => 'Template Kit not found',
            ], 404);
        }

        $templateKit->delete();

        return response()->json([
            'message' => 'Template Kit deleted successfully',
        ], 200);
    }
}
