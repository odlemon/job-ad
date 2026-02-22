<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\JobApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    public function __construct(
        private JobApplicationService $service
    ) {
    }

    /**
     * Display a listing of job applications.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $applications = $this->service->getPaginated($perPage);

        return response()->json($applications);
    }

    /**
     * Store a newly created job application.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'job_advertisement_id' => 'required|exists:job_advertisements,id',
            'user_id' => 'nullable|exists:users,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'cover_letter' => 'nullable|string',
            'resume_path' => 'nullable|string|max:255',
            'additional_info' => 'nullable|array',
            'status' => 'nullable|in:applied,in_review,interview,offered,rejected',
        ]);

        $application = $this->service->create($validated);

        return response()->json($application, 201);
    }

    /**
     * Display the specified job application.
     */
    public function show(int $id): JsonResponse
    {
        $application = $this->service->getById($id);

        if (!$application) {
            return response()->json(['message' => 'Job application not found'], 404);
        }

        return response()->json($application);
    }

    /**
     * Update the specified job application.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $application = $this->service->getById($id);

        if (!$application) {
            return response()->json(['message' => 'Job application not found'], 404);
        }

        $validated = $request->validate([
            'job_advertisement_id' => 'sometimes|required|exists:job_advertisements,id',
            'user_id' => 'nullable|exists:users,id',
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'cover_letter' => 'nullable|string',
            'resume_path' => 'nullable|string|max:255',
            'additional_info' => 'nullable|array',
            'status' => 'nullable|in:applied,in_review,interview,offered,rejected',
            'notes' => 'nullable|string',
        ]);

        $application = $this->service->update($application, $validated);

        return response()->json($application);
    }

    /**
     * Remove the specified job application.
     */
    public function destroy(int $id): JsonResponse
    {
        $application = $this->service->getById($id);

        if (!$application) {
            return response()->json(['message' => 'Job application not found'], 404);
        }

        $this->service->delete($application);

        return response()->json(['message' => 'Job application deleted successfully'], 200);
    }
}
