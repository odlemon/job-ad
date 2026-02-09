<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\JobAdvertisementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobAdvertisementController extends Controller
{
    public function __construct(
        private JobAdvertisementService $service
    ) {
    }

    /**
     * Display a listing of job advertisements.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $jobs = $this->service->getPaginated($perPage);

        return response()->json($jobs);
    }

    /**
     * Display published job advertisements.
     */
    public function published(): JsonResponse
    {
        $jobs = $this->service->getPublished();

        return response()->json($jobs);
    }

    /**
     * Store a newly created job advertisement.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'category_id' => 'nullable|exists:job_categories,id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:job_advertisements,slug',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'employment_type' => 'nullable|string|max:255',
            'experience_level' => 'nullable|string|max:255',
            'salary_min' => 'nullable|string|max:255',
            'salary_max' => 'nullable|string|max:255',
            'currency' => 'nullable|string|max:3',
            'location' => 'nullable|string|max:255',
            'is_remote' => 'nullable|boolean',
            'application_deadline' => 'nullable|date',
            'status' => 'nullable|in:draft,published,closed,archived',
        ]);

        $job = $this->service->create($validated);

        return response()->json($job, 201);
    }

    /**
     * Display the specified job advertisement.
     */
    public function show(string $slug): JsonResponse
    {
        $job = $this->service->getBySlug($slug);

        if (!$job) {
            return response()->json(['message' => 'Job advertisement not found'], 404);
        }

        // Increment views when viewing
        $this->service->incrementViews($job);

        return response()->json($job);
    }

    /**
     * Update the specified job advertisement.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $job = $this->service->getById($id);

        if (!$job) {
            return response()->json(['message' => 'Job advertisement not found'], 404);
        }

        $validated = $request->validate([
            'company_id' => 'sometimes|required|exists:companies,id',
            'category_id' => 'nullable|exists:job_categories,id',
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|nullable|string|max:255|unique:job_advertisements,slug,' . $id,
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'employment_type' => 'nullable|string|max:255',
            'experience_level' => 'nullable|string|max:255',
            'salary_min' => 'nullable|string|max:255',
            'salary_max' => 'nullable|string|max:255',
            'currency' => 'nullable|string|max:3',
            'location' => 'nullable|string|max:255',
            'is_remote' => 'nullable|boolean',
            'application_deadline' => 'nullable|date',
            'status' => 'nullable|in:draft,published,closed,archived',
        ]);

        $job = $this->service->update($job, $validated);

        return response()->json($job);
    }

    /**
     * Remove the specified job advertisement.
     */
    public function destroy(int $id): JsonResponse
    {
        $job = $this->service->getById($id);

        if (!$job) {
            return response()->json(['message' => 'Job advertisement not found'], 404);
        }

        $this->service->delete($job);

        return response()->json(['message' => 'Job advertisement deleted successfully'], 200);
    }
}
