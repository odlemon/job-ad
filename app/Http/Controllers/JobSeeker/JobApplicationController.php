<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Models\JobAdvertisement;
use App\Services\JobSeeker\JobSeekerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobApplicationController extends Controller
{
    public function __construct(
        private JobSeekerService $jobSeekerService
    ) {
        // Middleware is already applied in routes
    }

    /**
     * Show the job application form.
     */
    public function show(int $id)
    {
        $user = Auth::user();

        // Check if user is a job seeker
        if (!$user || $user->user_type !== 'job_seeker') {
            return redirect()->route('dashboard')
                ->with('error', 'Only job seekers can apply to jobs.');
        }

        // Check if job seeker profile exists
        $jobSeeker = $this->jobSeekerService->getByUserId($user->id);
        if (!$jobSeeker) {
            return redirect()->route('job-seeker.profile')
                ->with('error', 'Please complete your profile before applying to jobs.');
        }

        // Get the job advertisement
        $job = JobAdvertisement::with(['company', 'category'])->find($id);

        if (!$job) {
            return redirect()->route('jobs.index')
                ->with('error', 'Job posting not found.');
        }

        // Check if job is published
        if ($job->status !== 'published') {
            return redirect()->route('jobs.show', $id)
                ->with('error', 'This job posting is not available for applications.');
        }

        // Check if already applied
        $existingApplication = $jobSeeker->applications()
            ->where('job_advertisement_id', $id)
            ->first();

        if ($existingApplication) {
            return redirect()->route('job-seeker.applications')
                ->with('info', 'You have already applied to this job.');
        }

        // Get application questions from job
        $questions = $job->application_questions ?? [];
        
        // Ensure questions is an array
        if (is_string($questions)) {
            $questions = json_decode($questions, true) ?? [];
        }
        
        if (!is_array($questions)) {
            $questions = [];
        }

        return view('jobs.apply', [
            'job' => $job,
            'jobSeeker' => $jobSeeker,
            'questions' => $questions,
        ]);
    }
}
