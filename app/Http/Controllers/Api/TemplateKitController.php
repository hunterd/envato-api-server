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
                'industries' => 'nullable|array',
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
                'industries' => 'nullable|array',
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

    /**
     * Search for template kits (Envato Elements API compatible endpoint)
     */
    public function search(Request $request): JsonResponse
    {
        $query = TemplateKit::query();

        // Filter by type (wordpress) - we only support wordpress template kits
        if ($request->has('type') && $request->type !== 'wordpress') {
            return response()->json([
                'data' => [],
                'meta' => [
                    'current_page' => 1,
                    'total' => 0,
                    'per_page' => 15,
                ],
            ]);
        }

        // Filter by categories (Template Kits)
        if ($request->has('categories')) {
            $query->where('category', $request->categories);
        }

        // Filter by industries
        if ($request->has('industries') && ! empty($request->industries)) {
            $industries = is_array($request->industries) ? $request->industries : explode(',', $request->industries);
            $query->where(function ($q) use ($industries) {
                foreach ($industries as $industry) {
                    $q->orWhereJsonContains('industries', trim($industry));
                }
            });
        }

        // Search by search_terms
        if ($request->has('search_terms') && ! empty($request->search_terms)) {
            $searchTerm = $request->search_terms;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%'.$searchTerm.'%')
                    ->orWhere('description', 'like', '%'.$searchTerm.'%')
                    ->orWhere('author', 'like', '%'.$searchTerm.'%');
            });
        }

        // Filter by tags
        if ($request->has('tags') && ! empty($request->tags)) {
            $tags = is_array($request->tags) ? $request->tags : explode(',', $request->tags);
            $query->where(function ($q) use ($tags) {
                foreach ($tags as $tag) {
                    $q->orWhereJsonContains('tags', trim($tag));
                }
            });
        }

        // Only return active template kits
        $query->where('is_active', true);

        // Pagination
        $page = $request->get('page', 1);
        $perPage = 15;

        // Limit page number to max 50
        if ($page > 50) {
            $page = 50;
        }

        $templateKits = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $templateKits->items(),
            'meta' => [
                'current_page' => $templateKits->currentPage(),
                'total' => $templateKits->total(),
                'per_page' => $templateKits->perPage(),
                'last_page' => $templateKits->lastPage(),
            ],
        ]);
    }
}
