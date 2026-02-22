<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Services\JobApplicationService;
use App\Services\JobAdvertisementService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployerApplicationController extends Controller
{
    public function __construct(
        private JobApplicationService $applicationService,
        private JobAdvertisementService $jobService,
        private NotificationService $notificationService
    ) {
    }

    /**
     * Display a listing of applications for the employer's jobs.
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
        $jobId = $request->get('job_id');
        
        // Get all applications for company's jobs with relationships
        $applications = $this->applicationService->getByCompanyId($employer->company_id);
        $applications->load(['jobSeeker.experiences', 'jobAdvertisement.company']);
        
        // Filter by job if specified
        if ($jobId) {
            $applications = $applications->filter(function ($application) use ($jobId) {
                return $application->job_advertisement_id == $jobId;
            });
        }
        
        // Filter by status
        if ($status !== 'all' && $status !== 'talent_pool') {
            $applications = $applications->filter(function ($application) use ($status) {
                return $application->status === $status;
            });
        } elseif ($status === 'talent_pool') {
            // Talent pool not implemented yet, return empty
            $applications = collect([]);
        }
        
        // Get stats
        $allApplications = $this->applicationService->getByCompanyId($employer->company_id);
        $today = now()->startOfDay();
        $stats = [
            'all' => $allApplications->count(),
            'pending' => $allApplications->where('status', 'pending')->count(),
            'reviewing' => $allApplications->where('status', 'reviewing')->count(),
            'shortlisted' => $allApplications->where('status', 'shortlisted')->count(),
            'rejected' => $allApplications->where('status', 'rejected')->count(),
            'hired' => $allApplications->where('status', 'hired')->count(),
            'new_today' => $allApplications->filter(function($app) use ($today) {
                return $app->created_at >= $today;
            })->count(),
        ];
        
        // Get jobs for filter dropdown
        $jobs = $this->jobService->getByCompanyId($employer->company_id);
        
        return view('employer.applications.index', [
            'applications' => $applications->values(),
            'stats' => $stats,
            'currentStatus' => $status,
            'currentJobId' => $jobId,
            'jobs' => $jobs,
        ]);
    }

    /**
     * Display the specified application.
     */
    public function show(Request $request, int $id)
    {
        $user = Auth::user();
        $employer = $user->employer;
        
        $application = $this->applicationService->getById($id);
        
        if (!$application) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['message' => 'Application not found'], 404);
            }
            abort(404, 'Application not found');
        }
        
        // Ensure the application belongs to the employer's company
        if ($application->jobAdvertisement->company_id !== $employer->company_id) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            abort(403, 'Unauthorized');
        }
        
        // Load relationships with job seeker profile details
        $application->load([
            'jobAdvertisement.company', 
            'jobAdvertisement.category', 
            'jobSeeker.experiences',
            'jobSeeker.educations',
            'jobSeeker.skills',
            'jobSeeker.languages',
            'jobSeeker.certifications',
            'jobSeeker.references',
            'user', 
            'reviewer'
        ]);
        
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'application' => $application,
            ]);
        }
        
        return view('employer.applications.show', [
            'application' => $application,
        ]);
    }

    /**
     * Update the application status.
     */
    public function updateStatus(Request $request, int $id)
    {
        $user = Auth::user();
        $employer = $user->employer;
        
        $application = $this->applicationService->getById($id);
        
        if (!$application) {
            return response()->json(['message' => 'Application not found'], 404);
        }
        
        // Ensure the application belongs to the employer's company
        if ($application->jobAdvertisement->company_id !== $employer->company_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,reviewing,shortlisted,rejected,hired',
            'employer_notes' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Store old status before update
        $oldStatus = $application->status;

        // Set reviewed_at when status changes from pending
        if ($application->status === 'pending' && $validated['status'] !== 'pending') {
            $validated['reviewed_at'] = now();
            $validated['reviewed_by'] = $user->id;
        }

        $updatedApplication = $this->applicationService->update($application, $validated);

        // Load all relationships needed for the modal
        $updatedApplication->load([
            'jobAdvertisement.company', 
            'jobAdvertisement.category', 
            'jobSeeker.experiences',
            'jobSeeker.educations',
            'jobSeeker.skills',
            'jobSeeker.languages',
            'jobSeeker.certifications',
            'jobSeeker.references',
            'user', 
            'reviewer'
        ]);
        
        // Create notification for job seeker if status changed
        if (isset($validated['status']) && $oldStatus !== $validated['status']) {
            // Get user_id from job seeker relationship or directly
            $jobSeekerUserId = null;
            if ($updatedApplication->jobSeeker && $updatedApplication->jobSeeker->user_id) {
                $jobSeekerUserId = $updatedApplication->jobSeeker->user_id;
            } elseif ($updatedApplication->user_id) {
                $jobSeekerUserId = $updatedApplication->user_id;
            }
            
            if ($jobSeekerUserId) {
                $this->notificationService->notifyStatusUpdate(
                    $jobSeekerUserId,
                    $updatedApplication->id,
                    $validated['status'],
                    $updatedApplication->jobAdvertisement->title,
                    $updatedApplication->jobAdvertisement->company->name
                );
            }
        }

        return response()->json([
            'message' => 'Application status updated successfully',
            'application' => $updatedApplication,
        ], 200);
    }

    /**
     * Get filtered applications via AJAX.
     */
    public function getApplications(Request $request)
    {
        $user = Auth::user();
        $employer = $user->employer;
        
        if (!$employer || !$employer->company_id) {
            return response()->json(['message' => 'Company profile not set up'], 403);
        }

        $status = $request->get('status', 'all');
        $jobId = $request->get('job_id');
        $search = $request->get('search', '');
        
        // Get all applications for company's jobs with relationships
        $applications = $this->applicationService->getByCompanyId($employer->company_id);
        $applications->load(['jobSeeker.experiences', 'jobAdvertisement.company']);
        
        // Filter by job if specified
        if ($jobId) {
            $applications = $applications->filter(function ($application) use ($jobId) {
                return $application->job_advertisement_id == $jobId;
            });
        }
        
        // Filter by status
        if ($status !== 'all' && $status !== 'talent_pool') {
            $applications = $applications->filter(function ($application) use ($status) {
                return $application->status === $status;
            });
        } elseif ($status === 'talent_pool') {
            $applications = collect([]);
        }
        
        // Filter by search term
        if ($search) {
            $searchLower = strtolower($search);
            $applications = $applications->filter(function ($application) use ($searchLower) {
                $fullName = strtolower($application->first_name . ' ' . $application->last_name);
                $jobTitle = strtolower($application->jobAdvertisement->title ?? '');
                $companyName = strtolower($application->jobAdvertisement->company->name ?? '');
                $email = strtolower($application->email ?? '');
                
                return str_contains($fullName, $searchLower) ||
                       str_contains($jobTitle, $searchLower) ||
                       str_contains($companyName, $searchLower) ||
                       str_contains($email, $searchLower);
            });
        }
        
        return response()->json([
            'applications' => $applications->values()->map(function ($application) {
                $initials = strtoupper(substr($application->first_name, 0, 1) . substr($application->last_name, 0, 1));
                
                // Calculate experience
                $experience = 'N/A';
                try {
                    if ($application->jobSeeker && $application->jobSeeker->relationLoaded('experiences') && $application->jobSeeker->experiences) {
                        $totalYears = 0;
                        foreach ($application->jobSeeker->experiences as $exp) {
                            if ($exp && isset($exp->start_date) && $exp->start_date) {
                                $start = new \DateTime($exp->start_date);
                                $end = (isset($exp->end_date) && $exp->end_date) ? new \DateTime($exp->end_date) : new \DateTime();
                                $diff = $start->diff($end);
                                $totalYears += $diff->y;
                            }
                        }
                        $experience = $totalYears > 0 ? $totalYears . ' years' : 'N/A';
                    }
                } catch (\Exception $e) {
                    $experience = 'N/A';
                }
                
                return [
                    'id' => $application->id,
                    'initials' => $initials,
                    'first_name' => $application->first_name,
                    'last_name' => $application->last_name,
                    'email' => $application->email,
                    'status' => $application->status,
                    'job_seeker' => $application->jobSeeker ? [
                        'profile_photo' => $application->jobSeeker->profile_photo,
                        'bio' => $application->jobSeeker->bio,
                        'location' => $application->jobSeeker->location,
                        'experiences' => $application->jobSeeker->experiences,
                        'educations' => $application->jobSeeker->educations,
                        'skills' => $application->jobSeeker->skills,
                        'languages' => $application->jobSeeker->languages,
                        'certifications' => $application->jobSeeker->certifications,
                    ] : null,
                    'job_title' => $application->jobAdvertisement->title ?? 'N/A',
                    'job_id' => $application->jobAdvertisement->id ?? null,
                    'company_name' => $application->jobAdvertisement->company->name ?? 'N/A',
                    'location' => $application->jobAdvertisement->is_remote ? 'Remote' : ($application->jobAdvertisement->location ?? 'Not specified'),
                    'applied_date' => $application->created_at->format('Y-m-d'),
                    'experience' => $experience,
                    'rating' => 4, // Default rating
                ];
            }),
        ]);
    }

    /**
     * Export applications to XLSX.
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $employer = $user->employer;
        
        if (!$employer || !$employer->company_id) {
            return redirect()->route('employer.applications.index')
                ->with('error', 'Please set up your company profile first.');
        }

        $status = $request->get('status', 'all');
        $jobId = $request->get('job_id');
        $search = $request->get('search', '');
        
        // Get all applications for company's jobs with relationships
        $applications = $this->applicationService->getByCompanyId($employer->company_id);
        $applications->load(['jobSeeker.experiences', 'jobAdvertisement.company']);
        
        // Apply filters
        if ($jobId) {
            $applications = $applications->filter(function ($application) use ($jobId) {
                return $application->job_advertisement_id == $jobId;
            });
        }
        
        if ($status !== 'all' && $status !== 'talent_pool') {
            $applications = $applications->filter(function ($application) use ($status) {
                return $application->status === $status;
            });
        } elseif ($status === 'talent_pool') {
            $applications = collect([]);
        }
        
        if ($search) {
            $searchLower = strtolower($search);
            $applications = $applications->filter(function ($application) use ($searchLower) {
                $fullName = strtolower($application->first_name . ' ' . $application->last_name);
                $jobTitle = strtolower($application->jobAdvertisement->title ?? '');
                $companyName = strtolower($application->jobAdvertisement->company->name ?? '');
                $email = strtolower($application->email ?? '');
                
                return str_contains($fullName, $searchLower) ||
                       str_contains($jobTitle, $searchLower) ||
                       str_contains($companyName, $searchLower) ||
                       str_contains($email, $searchLower);
            });
        }
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $headers = [
            'A1' => 'Application ID',
            'B1' => 'Full Name',
            'C1' => 'Email',
            'D1' => 'Phone',
            'E1' => 'Job Title',
            'F1' => 'Job ID',
            'G1' => 'Company',
            'H1' => 'Location',
            'I1' => 'Status',
            'J1' => 'Applied Date',
            'K1' => 'Experience',
            'L1' => 'Cover Letter',
            'M1' => 'Notes',
        ];
        
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        
        // Style header row
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2563EB'], // Blue-600
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
            ],
        ];
        
        $sheet->getStyle('A1:M1')->applyFromArray($headerStyle);
        
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(25);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(15);
        $sheet->getColumnDimension('K')->setWidth(15);
        $sheet->getColumnDimension('L')->setWidth(50);
        $sheet->getColumnDimension('M')->setWidth(50);
        
        // Add data rows
        $row = 2;
        foreach ($applications as $application) {
            // Calculate experience
            $experience = 'N/A';
            try {
                if ($application->jobSeeker && $application->jobSeeker->relationLoaded('experiences') && $application->jobSeeker->experiences) {
                    $totalYears = 0;
                    foreach ($application->jobSeeker->experiences as $exp) {
                        if ($exp && isset($exp->start_date) && $exp->start_date) {
                            $start = new \DateTime($exp->start_date);
                            $end = (isset($exp->end_date) && $exp->end_date) ? new \DateTime($exp->end_date) : new \DateTime();
                            $diff = $start->diff($end);
                            $totalYears += $diff->y;
                        }
                    }
                    $experience = $totalYears > 0 ? $totalYears . ' years' : 'N/A';
                }
            } catch (\Exception $e) {
                $experience = 'N/A';
            }
            
            $statusLabels = [
                'pending' => 'Pending',
                'reviewing' => 'Reviewing',
                'shortlisted' => 'Shortlisted',
                'rejected' => 'Rejected',
                'hired' => 'Hired',
            ];
            
            $sheet->setCellValue('A' . $row, 'APP-' . str_pad($application->id, 4, '0', STR_PAD_LEFT));
            $sheet->setCellValue('B' . $row, $application->first_name . ' ' . $application->last_name);
            $sheet->setCellValue('C' . $row, $application->email ?? '');
            $sheet->setCellValue('D' . $row, $application->phone ?? '');
            $sheet->setCellValue('E' . $row, $application->jobAdvertisement->title ?? 'N/A');
            $sheet->setCellValue('F' . $row, 'JOB-' . str_pad($application->jobAdvertisement->id ?? 0, 3, '0', STR_PAD_LEFT));
            $sheet->setCellValue('G' . $row, $application->jobAdvertisement->company->name ?? 'N/A');
            $sheet->setCellValue('H' . $row, $application->jobAdvertisement->is_remote ? 'Remote' : ($application->jobAdvertisement->location ?? 'Not specified'));
            $sheet->setCellValue('I' . $row, $statusLabels[$application->status] ?? ucfirst($application->status));
            $sheet->setCellValue('J' . $row, $application->created_at->format('Y-m-d H:i:s'));
            $sheet->setCellValue('K' . $row, $experience);
            $sheet->setCellValue('L' . $row, $application->cover_letter ?? '');
            $sheet->setCellValue('M' . $row, $application->employer_notes ?? '');
            
            // Style data rows
            $sheet->getStyle('A' . $row . ':M' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E5E7EB'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_TOP,
                ],
            ]);
            
            // Alternate row colors
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':M' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F9FAFB'],
                    ],
                ]);
            }
            
            // Wrap text for long columns
            $sheet->getStyle('L' . $row . ':M' . $row)->getAlignment()->setWrapText(true);
            
            $row++;
        }
        
        // Set row height for header
        $sheet->getRowDimension(1)->setRowHeight(25);
        
        $filename = 'applications_' . now()->format('Y-m-d_His') . '.xlsx';
        
        $response = new StreamedResponse(function() use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });
        
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');
        
        return $response;
    }
}
