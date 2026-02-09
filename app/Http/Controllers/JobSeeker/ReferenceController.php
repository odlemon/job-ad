<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Models\JobSeekerReference;
use App\Services\JobSeeker\JobSeekerService;
use App\Services\JobSeeker\ReferenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReferenceController extends Controller
{
    public function __construct(
        private ReferenceService $referenceService,
        private JobSeekerService $jobSeekerService
    ) {
    }

    public function index(): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json(['message' => 'Job seeker profile not found'], 404);
        }

        $references = $this->referenceService->getBySeeker($jobSeeker);

        return response()->json(['data' => $references]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json(['message' => 'Job seeker profile not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'reference_name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'relationship' => 'required|in:former_manager,former_colleague,former_client,academic,other',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $reference = $this->referenceService->create($jobSeeker, $validator->validated());

        return response()->json([
            'message' => 'Reference added successfully',
            'data' => $reference,
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json(['message' => 'Job seeker profile not found'], 404);
        }

        $reference = JobSeekerReference::where('seeker_id', $jobSeeker->seeker_id)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'reference_name' => 'sometimes|required|string|max:255',
            'title' => 'sometimes|required|string|max:255',
            'company' => 'sometimes|required|string|max:255',
            'relationship' => 'sometimes|required|in:former_manager,former_colleague,former_client,academic,other',
            'email' => 'sometimes|required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $reference = $this->referenceService->update($reference, $validator->validated());

        return response()->json([
            'message' => 'Reference updated successfully',
            'data' => $reference,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json(['message' => 'Job seeker profile not found'], 404);
        }

        $reference = JobSeekerReference::where('seeker_id', $jobSeeker->seeker_id)
            ->findOrFail($id);

        $this->referenceService->delete($reference);

        return response()->json(['message' => 'Reference deleted successfully']);
    }
}
