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
use Illuminate\Support\Str;

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

        $certifications = $this->certificationService->getBySeeker($jobSeeker)
            ->map(fn ($c) => \App\Support\ScoopNestedPresenter::certification($c))
            ->values();

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
            'certification_name' => 'required_without:name|string|max:255',
            'name' => 'required_without:certification_name|string|max:255',
            'issuing_organization' => 'required_without:issuer|string|max:255',
            'issuer' => 'required_without:issuing_organization|string|max:255',
            'issue_date' => 'required_without:issued_at|date',
            'issued_at' => 'required_without:issue_date|date',
            'expiry_date' => 'nullable|date',
            'expires_at' => 'nullable|date',
            'certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'credential_id' => 'nullable|string|max:255',
            'credential_url' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = [
            'certification_name' => $request->input('certification_name', $request->input('name')),
            'issuing_organization' => $request->input('issuing_organization', $request->input('issuer')),
            'issue_date' => $request->input('issue_date', $request->input('issued_at')),
            'expiry_date' => $request->input('expiry_date', $request->input('expires_at')),
            'credential_id' => $request->input('credential_id'),
            'credential_url' => $request->input('credential_url'),
        ];

        // Normalize credential_url to allow URLs without scheme (e.g. example.com/cert)
        if (!empty($data['credential_url'])) {
            $url = trim($data['credential_url']);
            if (!Str::startsWith($url, ['http://', 'https://'])) {
                $url = 'https://' . $url;
            }
            $data['credential_url'] = $url;
        }
        $certificateFile = $request->hasFile('certificate_file') ? $request->file('certificate_file') : null;

        $certification = $this->certificationService->create($jobSeeker, $data, $certificateFile);

        return response()->json([
            'message' => 'Certification added successfully',
            'data' => \App\Support\ScoopNestedPresenter::certification($certification),
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
            'credential_url' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Normalize credential_url to allow URLs without scheme
        if (!empty($data['credential_url'])) {
            $url = trim($data['credential_url']);
            if (!Str::startsWith($url, ['http://', 'https://'])) {
                $url = 'https://' . $url;
            }
            $data['credential_url'] = $url;
        }
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
