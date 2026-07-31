<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\JobCampaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;

/**
 * Admin: invite applicant (same as employer; no company check).
 */
class AdminApplicationController extends Controller
{
    /**
     * Send invite email to an applicant to apply to the job (same as employer invite).
     */
    public function inviteApplicant(Request $request, int $id): JsonResponse
    {
        $application = JobApplication::with(['jobAdvertisement.company'])
            ->where('id', $id)
            ->where('in_talent_pool', true)
            ->first();

        if (!$application) {
            return response()->json(['message' => 'Application not found or not in talent pool'], 404);
        }

        $job = $application->jobAdvertisement;
        $companyName = $job->company->name ?? 'Our company';
        $jobTitle = $job->title;
        $applicantName = trim($application->first_name . ' ' . $application->last_name) ?: 'Candidate';
        $applicantEmail = $application->email;

        if (!$applicantEmail) {
            return response()->json(['message' => 'Applicant has no email'], 400);
        }

        $applyUrl = url('/jobs/' . $job->id);
        $htmlBody = View::make('emails.invite-applicant', [
            'applicantName' => $applicantName,
            'jobTitle' => $jobTitle,
            'companyName' => $companyName,
            'applyUrl' => $applyUrl,
        ])->render();

        $subject = "You're invited to apply: {$jobTitle} at {$companyName}";
        $fromAddress = config('mail.from.address', 'noreply@kyntaro.com');
        $fromName = config('mail.from.name', 'JobHub');
        $apiToken = config('mail.mailers.smtp.password');

        try {
            $response = Http::withOptions(['verify' => false])
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Zoho-enczapikey ' . $apiToken,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.zeptomail.com/v1.1/email', [
                    'from' => [
                        'address' => $fromAddress,
                        'name' => $fromName,
                    ],
                    'to' => [
                        [
                            'email_address' => [
                                'address' => $applicantEmail,
                                'name' => $applicantName,
                            ],
                        ],
                    ],
                    'subject' => $subject,
                    'htmlbody' => $htmlBody,
                ]);

            if (!$response->successful()) {
                $body = $response->json() ?? $response->body();
                $errMsg = is_array($body) ? ($body['message'] ?? $body['error'] ?? json_encode($body)) : $body;
                throw new \RuntimeException('ZeptoMail API error: ' . $errMsg);
            }
        } catch (\Exception $e) {
            \Log::error('Admin invite applicant email failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to send invite email',
                'error' => $e->getMessage(),
            ], 500);
        }

        $application->invite_sent_at = now();
        $application->save();

        $campaign = JobCampaign::where('job_advertisement_id', $job->id)
            ->orderByDesc('launched_at')
            ->first();
        if ($campaign) {
            $campaign->increment('invitation_sent_count');
        }

        return response()->json([
            'message' => 'Invitation sent successfully',
        ]);
    }
}
