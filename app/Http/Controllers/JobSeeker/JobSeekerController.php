<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Models\JobSeekerDocument;
use App\Services\JobSeeker\JobSeekerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JobSeekerController extends Controller
{
    public function __construct(
        private JobSeekerService $service
    ) {
    }

    /**
     * Get authenticated job seeker profile.
     */
    public function profile(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || $user->user_type !== 'job_seeker') {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $jobSeeker = $this->service->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json([
                'message' => 'Job seeker profile not found',
            ], 404);
        }

        // Load relationships (include documents for profile page)
        $jobSeeker->load(['user', 'documents']);

        return response()->json([
            'data' => $jobSeeker,
            'job_seeker' => $jobSeeker, // Keep for backward compatibility
        ]);
    }

    /**
     * Update job seeker profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || $user->user_type !== 'job_seeker') {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $jobSeeker = $this->service->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json([
                'message' => 'Job seeker profile not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'gender' => 'nullable|in:male,female,other,prefer_not_to_say',
            'date_of_birth' => 'nullable|date|before:today',
            'employment_status' => 'nullable|in:currently_employed,unemployed,student,self_employed,retired',
            'highest_education' => 'nullable|string|max:255',
            'driving_license' => 'nullable|boolean',
            'license_issued_date' => 'nullable|date',
            'job_preferences' => 'nullable|array',
            'job_preferences.*' => 'in:full_time,part_time,contract',
            'expected_salary_min' => 'nullable|integer|min:0',
            'expected_salary_max' => 'nullable|integer|min:0',
            'linkedin_url' => 'nullable|string|max:500',
            'facebook_url' => 'nullable|string|max:500',
            'instagram_url' => 'nullable|string|max:500',
            'website_url' => 'nullable|string|max:500',
            'public_profile' => 'nullable|boolean',
            'open_to_opportunities' => 'nullable|boolean',
            'hobbies' => 'nullable|array',
            'bio' => 'nullable|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Get data and convert empty strings to null for nullable fields
        $data = $request->only([
            'first_name',
            'last_name',
            'location',
            'phone',
            'address',
            'gender',
            'date_of_birth',
            'employment_status',
            'highest_education',
            'driving_license',
            'license_issued_date',
            'job_preferences',
            'expected_salary_min',
            'expected_salary_max',
            'linkedin_url',
            'facebook_url',
            'instagram_url',
            'website_url',
            'public_profile',
            'open_to_opportunities',
            'hobbies',
            'bio',
        ]);

        // Convert empty strings to null for nullable date/string fields
        $nullableFields = ['phone', 'address', 'gender', 'date_of_birth', 'employment_status',
                          'highest_education', 'license_issued_date', 'linkedin_url', 'facebook_url', 'instagram_url', 'website_url'];
        foreach ($nullableFields as $field) {
            if (isset($data[$field]) && $data[$field] === '') {
                $data[$field] = null;
            }
        }

        // Normalize social URLs: add https:// if missing (so we accept "facebook.com/user")
        $socialUrlFields = ['linkedin_url', 'facebook_url', 'instagram_url', 'website_url'];
        foreach ($socialUrlFields as $field) {
            if (! empty($data[$field]) && ! preg_match('#^https?://#i', $data[$field])) {
                $data[$field] = 'https://' . ltrim($data[$field], '/');
            }
        }

        $jobSeeker = $this->service->updateProfile($jobSeeker, $data);

        $jobSeeker->load('user');

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => $jobSeeker,
            'job_seeker' => $jobSeeker, // Keep for backward compatibility
        ]);
    }

    /**
     * Upload CV.
     */
    public function uploadCv(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || $user->user_type !== 'job_seeker') {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $jobSeeker = $this->service->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json([
                'message' => 'Job seeker profile not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'cv' => 'required|file|mimes:pdf,doc,docx|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $jobSeeker = $this->service->uploadCv($jobSeeker, $request->file('cv'));
            $jobSeeker->load('user');

            return response()->json([
                'message' => 'CV uploaded successfully',
                'data' => $jobSeeker,
                'job_seeker' => $jobSeeker, // Keep for backward compatibility
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'CV upload failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete CV.
     */
    public function deleteCv(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || $user->user_type !== 'job_seeker') {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $jobSeeker = $this->service->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json([
                'message' => 'Job seeker profile not found',
            ], 404);
        }

        $jobSeeker = $this->service->deleteCv($jobSeeker);
        $jobSeeker->load('user');

        return response()->json([
            'message' => 'CV deleted successfully',
            'data' => $jobSeeker,
            'job_seeker' => $jobSeeker, // Keep for backward compatibility
        ]);
    }

    /**
     * List documents for the job seeker.
     */
    public function documents(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user || $user->user_type !== 'job_seeker') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $jobSeeker = $this->service->getByUserId($user->id);
        if (!$jobSeeker) {
            return response()->json(['message' => 'Job seeker profile not found'], 404);
        }
        $documents = $jobSeeker->documents()->orderByDesc('is_primary')->orderByDesc('created_at')->get();
        return response()->json(['data' => $documents]);
    }

    /**
     * Upload a document with a user-provided name.
     */
    public function storeDocument(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user || $user->user_type !== 'job_seeker') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $jobSeeker = $this->service->getByUserId($user->id);
        if (!$jobSeeker) {
            return response()->json(['message' => 'Job seeker profile not found'], 404);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240', // 10MB
            'is_primary' => 'nullable|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }
        try {
            $document = $this->service->addDocument(
                $jobSeeker,
                $request->input('name'),
                $request->file('file'),
                (bool) $request->boolean('is_primary')
            );
            return response()->json([
                'message' => 'Document uploaded successfully',
                'data' => $document,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Document upload failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a document.
     */
    public function deleteDocument(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();
        if (!$user || $user->user_type !== 'job_seeker') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $jobSeeker = $this->service->getByUserId($user->id);
        if (!$jobSeeker) {
            return response()->json(['message' => 'Job seeker profile not found'], 404);
        }
        $document = JobSeekerDocument::where('seeker_id', $jobSeeker->seeker_id)->find($id);
        if (!$document) {
            return response()->json(['message' => 'Document not found'], 404);
        }
        $this->service->deleteDocument($jobSeeker, $document);
        return response()->json(['message' => 'Document deleted successfully']);
    }

    /**
     * Set a document as primary (resume used for applications).
     */
    public function setPrimaryDocument(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();
        if (!$user || $user->user_type !== 'job_seeker') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $jobSeeker = $this->service->getByUserId($user->id);
        if (!$jobSeeker) {
            return response()->json(['message' => 'Job seeker profile not found'], 404);
        }
        $document = JobSeekerDocument::where('seeker_id', $jobSeeker->seeker_id)->find($id);
        if (!$document) {
            return response()->json(['message' => 'Document not found'], 404);
        }
        $jobSeeker = $this->service->setPrimaryDocument($jobSeeker, $document);
        $jobSeeker->load('user');
        return response()->json([
            'message' => 'Primary document updated',
            'data' => $jobSeeker,
            'documents' => $jobSeeker->documents()->orderByDesc('is_primary')->orderByDesc('created_at')->get(),
        ]);
    }

    /**
     * Upload profile photo.
     */
    public function uploadProfilePhoto(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || $user->user_type !== 'job_seeker') {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $jobSeeker = $this->service->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json([
                'message' => 'Job seeker profile not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'profile_photo' => 'required|file|mimes:jpeg,jpg,png,webp|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $jobSeeker = $this->service->uploadProfilePhoto($jobSeeker, $request->file('profile_photo'));
            $jobSeeker->load('user');

            return response()->json([
                'message' => 'Profile photo uploaded successfully',
                'data' => $jobSeeker,
                'job_seeker' => $jobSeeker, // Keep for backward compatibility
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Profile photo upload failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete profile photo.
     */
    public function deleteProfilePhoto(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || $user->user_type !== 'job_seeker') {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $jobSeeker = $this->service->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json([
                'message' => 'Job seeker profile not found',
            ], 404);
        }

        $jobSeeker = $this->service->deleteProfilePhoto($jobSeeker);
        $jobSeeker->load('user');

        return response()->json([
            'message' => 'Profile photo deleted successfully',
            'data' => $jobSeeker,
            'job_seeker' => $jobSeeker, // Keep for backward compatibility
        ]);
    }

    /**
     * Delete job seeker profile.
     */
    public function deleteProfile(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || $user->user_type !== 'job_seeker') {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $jobSeeker = $this->service->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json([
                'message' => 'Job seeker profile not found',
            ], 404);
        }

        $this->service->deleteProfile($jobSeeker);

        // Delete user account
        $user->delete();

        return response()->json([
            'message' => 'Profile deleted successfully',
        ]);
    }
}
