<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Models\JobSeekerCertification;
use App\Services\JobSeeker\CertificationService;
use App\Services\JobSeeker\JobSeekerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CertificationController extends Controller
{
    public function __construct(
        private CertificationService $certificationService,
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

        $certifications = $this->certificationService->getBySeeker($jobSeeker);

        return response()->json(['data' => $certifications]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json(['message' => 'Job seeker profile not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'certification_name' => 'required|string|max:255',
            'issuing_organization' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'credential_id' => 'nullable|string|max:255',
            'credential_url' => 'nullable|url|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $certificateFile = $request->hasFile('certificate_file') ? $request->file('certificate_file') : null;
        unset($data['certificate_file']);

        $certification = $this->certificationService->create($jobSeeker, $data, $certificateFile);

        return response()->json([
            'message' => 'Certification added successfully',
            'data' => $certification,
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json(['message' => 'Job seeker profile not found'], 404);
        }

        $certification = JobSeekerCertification::where('seeker_id', $jobSeeker->seeker_id)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'certification_name' => 'sometimes|required|string|max:255',
            'issuing_organization' => 'sometimes|required|string|max:255',
            'issue_date' => 'sometimes|required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'credential_id' => 'nullable|string|max:255',
            'credential_url' => 'nullable|url|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $certificateFile = $request->hasFile('certificate_file') ? $request->file('certificate_file') : null;
        unset($data['certificate_file']);

        $certification = $this->certificationService->update($certification, $data, $certificateFile);

        return response()->json([
            'message' => 'Certification updated successfully',
            'data' => $certification,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $user = Auth::user();
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);

        if (!$jobSeeker) {
            return response()->json(['message' => 'Job seeker profile not found'], 404);
        }

        $certification = JobSeekerCertification::where('seeker_id', $jobSeeker->seeker_id)
            ->findOrFail($id);

        $this->certificationService->delete($certification);

        return response()->json(['message' => 'Certification deleted successfully']);
    }
}
