<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Services\JobAdvertisementService;
use App\Services\CompanyService;
use App\Services\JobCategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployerJobController extends Controller
{
    public function __construct(
        private JobAdvertisementService $jobService,
        private CompanyService $companyService,
        private JobCategoryService $categoryService
    ) {
    }

    /**
     * Display a listing of the employer's job postings.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $employer = $user->employer;
        
        if (!$employer || !$employer->company_id) {
            return redirect()->route('employer.dashboard')
                ->with('error', 'Please set up your company profile first.');
        }

        $status = $request->get('status', 'all');
        $search = $request->get('search', '');
        
        // Get jobs with application counts
        $jobs = \App\Models\JobAdvertisement::with(['company', 'category'])
            ->withCount('applications')
            ->where('company_id', $employer->company_id);
        
        // Apply search filter
        if ($search) {
            $jobs->where(function($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }
        
        // Filter by status - map 'active' to 'published'
        if ($status !== 'all') {
            $statusMap = ['active' => 'published', 'paused' => 'draft'];
            $actualStatus = $statusMap[$status] ?? $status;
            $jobs->where('status', $actualStatus);
        }
        
        $jobs = $jobs->orderBy('created_at', 'desc')->get();
        
        // Update application counts from actual count if needed
        foreach ($jobs as $job) {
            if ($job->applications_count === null) {
                $job->applications_count = $job->applications()->count();
            }
        }
        
        // Get stats
        $allJobs = \App\Models\JobAdvertisement::where('company_id', $employer->company_id)->get();
        $stats = [
            'all' => $allJobs->count(),
            'active' => $allJobs->where('status', 'published')->count(),
            'paused' => $allJobs->where('status', 'draft')->count(),
            'draft' => $allJobs->where('status', 'draft')->count(),
            'closed' => $allJobs->where('status', 'closed')->count(),
            'archived' => $allJobs->where('status', 'archived')->count(),
        ];
        
        $categories = $this->categoryService->getAll();
        
        return view('employer.jobs.index', [
            'jobs' => $jobs,
            'stats' => $stats,
            'currentStatus' => $status,
            'search' => $search,
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new job posting.
     */
    public function create()
    {
        $user = Auth::user();
        $employer = $user->employer;
        
        if (!$employer || !$employer->company_id) {
            return redirect()->route('employer.dashboard')
                ->with('error', 'Please set up your company profile first.');
        }

        $company = $this->companyService->getById($employer->company_id);
        $categories = $this->categoryService->getAll();
        
        return view('employer.jobs.create', [
            'company' => $company,
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created job posting.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $employer = $user->employer;
        
        if (!$employer || !$employer->company_id) {
            return redirect()->back()
                ->with('error', 'Please set up your company profile first.');
        }

        $validated = $request->validate([
            'category_id' => 'nullable|exists:job_categories,id',
            'title' => 'required|string|max:255',
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

        $validated['company_id'] = $employer->company_id;
        
        if (!isset($validated['status'])) {
            $validated['status'] = 'draft';
        }

        $job = $this->jobService->create($validated);
        
        // Load relationships and counts for JSON response
        $job->load(['company', 'category']);
        $job->applications_count = 0;
        $job->views_count = $job->views_count ?? 0;

        // If request expects JSON (AJAX), return JSON
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Job posting created successfully',
                'job' => $job,
            ], 201);
        }

        return redirect()->route('employer.jobs.index')
            ->with('success', 'Job posting created successfully.');
    }

    /**
     * Display the specified job posting.
     */
    public function show(Request $request, int $id)
    {
        $user = Auth::user();
        $employer = $user->employer;
        
        $job = $this->jobService->getById($id);
        
        if (!$job) {
            abort(404, 'Job posting not found');
        }
        
        // Ensure the job belongs to the employer's company
        if ($job->company_id !== $employer->company_id) {
            abort(403, 'Unauthorized');
        }
        
        // Load relationships
        $job->load(['company', 'category']);
        $job->applications_count = $job->applications()->count();
        $job->views_count = $job->views_count ?? 0;
        
        // If request expects JSON (AJAX), return JSON
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'job' => $job,
            ]);
        }
        
        return view('employer.jobs.show', [
            'job' => $job,
        ]);
    }

    /**
     * Show the form for editing the specified job posting.
     */
    public function edit(Request $request, int $id)
    {
        $user = Auth::user();
        $employer = $user->employer;
        
        $job = $this->jobService->getById($id);
        
        if (!$job) {
            abort(404, 'Job posting not found');
        }
        
        // Ensure the job belongs to the employer's company
        if ($job->company_id !== $employer->company_id) {
            abort(403, 'Unauthorized');
        }

        $categories = $this->categoryService->getAll();
        
        // If request expects JSON (AJAX), return JSON
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'job' => $job,
                'categories' => $categories,
            ]);
        }
        
        return view('employer.jobs.edit', [
            'job' => $job,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified job posting.
     */
    public function update(Request $request, int $id)
    {
        $user = Auth::user();
        $employer = $user->employer;
        
        $job = $this->jobService->getById($id);
        
        if (!$job) {
            abort(404, 'Job posting not found');
        }
        
        // Ensure the job belongs to the employer's company
        if ($job->company_id !== $employer->company_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'category_id' => 'nullable|exists:job_categories,id',
            'title' => 'required|string|max:255',
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

        $this->jobService->update($job, $validated);
        
        // Reload job with relationships
        $job = $this->jobService->getById($id);
        $job->load(['company', 'category']);
        $job->applications_count = $job->applications()->count();
        $job->views_count = $job->views_count ?? 0;

        // If request expects JSON (AJAX), return JSON
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Job posting updated successfully',
                'job' => $job,
            ]);
        }

        return redirect()->route('employer.jobs.index')
            ->with('success', 'Job posting updated successfully.');
    }

    /**
     * Remove the specified job posting.
     */
    public function destroy(int $id)
    {
        $user = Auth::user();
        $employer = $user->employer;
        
        $job = $this->jobService->getById($id);
        
        if (!$job) {
            return response()->json(['message' => 'Job posting not found'], 404);
        }
        
        // Ensure the job belongs to the employer's company
        if ($job->company_id !== $employer->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $this->jobService->delete($job);

        return response()->json(['message' => 'Job posting deleted successfully'], 200);
    }

    /**
     * Toggle job status (pause/resume).
     */
    public function toggleStatus(Request $request, int $id)
    {
        $user = Auth::user();
        $employer = $user->employer;
        
        $job = $this->jobService->getById($id);
        
        if (!$job) {
            return response()->json(['message' => 'Job posting not found'], 404);
        }
        
        // Ensure the job belongs to the employer's company
        if ($job->company_id !== $employer->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Toggle between published (active) and draft (paused)
        if ($job->status === 'published') {
            $job->status = 'draft';
            $message = 'Job paused successfully';
        } else {
            $job->status = 'published';
            if (!$job->published_at) {
                $job->published_at = now();
            }
            $message = 'Job activated successfully';
        }
        
        $job->save();

        return response()->json([
            'message' => $message,
            'status' => $job->status,
        ], 200);
    }
}
