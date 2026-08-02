<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('landing.index');
})->name('landing');

Route::get('/jobs', function () {
    return view('jobs.index');
})->name('jobs.index');

Route::get('/categories', [App\Http\Controllers\Public\CategoryPageController::class, 'index'])
    ->name('categories.index');

Route::get('/pricing', [App\Http\Controllers\Public\PricingPageController::class, 'index'])
    ->name('pricing.index');

Route::get('/tenders', [App\Http\Controllers\Public\TenderController::class, 'indexWeb'])
    ->name('tenders.index');

Route::get('/tenders/{idOrSlug}', [App\Http\Controllers\Public\TenderController::class, 'showWeb'])
    ->name('tenders.show');

Route::get('/companies', [App\Http\Controllers\Public\CompanyController::class, 'index'])
    ->name('companies.index');

Route::get('/companies/{company:slug}', [App\Http\Controllers\Public\CompanyController::class, 'show'])
    ->name('companies.show');

Route::get('/companies/{company:slug}/debug-review', [App\Http\Controllers\Public\CompanyController::class, 'debugReviewEligibility'])
    ->name('companies.debug-review')
    ->middleware('auth');

Route::post('/companies/{company:slug}/reviews', [App\Http\Controllers\Public\CompanyController::class, 'storeReview'])
    ->name('companies.reviews.store')
    ->middleware('auth');

Route::post('/companies/{company:slug}/follow', [App\Http\Controllers\Public\CompanyController::class, 'follow'])
    ->name('companies.follow')
    ->middleware('auth');

Route::delete('/companies/{company:slug}/follow', [App\Http\Controllers\Public\CompanyController::class, 'unfollow'])
    ->name('companies.unfollow')
    ->middleware('auth');

Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::get('/register', function () {
    return view('auth.register');
})->name('register')->middleware('guest');

// Test upload page (remove in production)
Route::get('/test-upload', function () {
    return view('test-upload');
})->middleware('auth');

// Web-based login POST route (handles session properly)
Route::post('/web/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        
        $user = Auth::user();
        $user->update(['last_login' => now()]);
        
        // Determine redirect based on user type
        $redirect = $user->user_type === 'employer' 
            ? '/employer/dashboard' 
            : '/dashboard';
        
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'redirect' => $redirect,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->user_type,
            ],
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Invalid credentials',
    ], 401);
})->middleware('guest');

// Web-based logout route (GET for simple redirect, POST for form submissions)
Route::match(['get', 'post'], '/auth/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    
    return redirect('/');  // Redirect to landing page
})->middleware('auth')->name('logout');

Route::get('/jobs/{id}', function ($id) {
    return view('jobs.show', ['id' => $id]);
})->name('jobs.show');

Route::get('/jobs/{id}/apply', [App\Http\Controllers\JobSeeker\JobApplicationController::class, 'show'])
    ->name('jobs.apply')
    ->middleware('auth');

// Job Seeker Routes (protected)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->user_type === 'employer') {
            return view('employer.dashboard');
        }
        return view('job-seeker.dashboard');
    })->name('dashboard');

    Route::get('/job-seeker', function () {
        return redirect()->route('dashboard');
    })->name('job-seeker.dashboard');

    Route::get('/job-seeker/dashboard', function () {
        return redirect()->route('dashboard');
    });
    
    Route::get('/job-seeker/profile', function () {
        return view('job-seeker.profile');
    })->name('job-seeker.profile');
    
    Route::get('/job-seeker/notifications', function () {
        return view('job-seeker.notifications');
    })->name('job-seeker.notifications');
    
    Route::get('/job-seeker/applications', [App\Http\Controllers\JobApplicationController::class, 'index'])->name('job-seeker.applications');
    Route::get('/job-seeker/applications/{id}', [App\Http\Controllers\JobApplicationController::class, 'show'])->name('job-seeker.application-detail');
    Route::put('/job-seeker/applications/{id}/notes', [App\Http\Controllers\JobApplicationController::class, 'updateNotes'])->name('job-seeker.application-notes');
    Route::delete('/job-seeker/applications/{id}', [App\Http\Controllers\JobApplicationController::class, 'destroy'])->name('job-seeker.application-delete');
    Route::post('/job-seeker/applications/{id}/interview-response', [App\Http\Controllers\JobApplicationController::class, 'interviewResponse'])->name('job-seeker.application-interview-response');

    Route::get('/job-seeker/discovery', [App\Http\Controllers\JobSeeker\DiscoveryController::class, 'index'])->name('job-seeker.discovery');

    // Notifications API moved to routes/api.php (Sanctum + session) for Scoop mobile + Blade

    Route::get('/job-seeker/saved-jobs', function () {
        return redirect()->route('job-seeker.discovery', ['tab' => 'saved']);
    })->name('job-seeker.saved-jobs');
    
    Route::get('/job-seeker/followed-companies', [App\Http\Controllers\JobSeeker\FollowedCompanyController::class, 'page'])
        ->name('job-seeker.followed-companies');

    Route::get('/job-seeker/career-tools', [App\Http\Controllers\JobSeeker\CareerToolsController::class, 'index'])
        ->name('job-seeker.career-tools');
    Route::get('/job-seeker/career-tools/bootstrap', [App\Http\Controllers\JobSeeker\CareerToolsController::class, 'bootstrap'])
        ->name('job-seeker.career-tools.bootstrap');
    Route::post('/job-seeker/career-tools/resume', [App\Http\Controllers\JobSeeker\CareerToolsController::class, 'generateResume'])
        ->name('job-seeker.career-tools.resume');
    Route::post('/job-seeker/career-tools/cover-letter', [App\Http\Controllers\JobSeeker\CareerToolsController::class, 'generateCoverLetter'])
        ->name('job-seeker.career-tools.cover-letter');
    Route::get('/job-seeker/career-tools/interview', [App\Http\Controllers\JobSeeker\CareerToolsController::class, 'interviewPrep'])
        ->name('job-seeker.career-tools.interview');
    Route::post('/job-seeker/career-tools/salary', [App\Http\Controllers\JobSeeker\CareerToolsController::class, 'calculateSalary'])
        ->name('job-seeker.career-tools.salary');
    Route::get('/job-seeker/career-tools/assessments/{topic}', [App\Http\Controllers\JobSeeker\CareerToolsController::class, 'assessmentQuestions'])
        ->name('job-seeker.career-tools.assessment');
    Route::post('/job-seeker/career-tools/assessments/{topic}/submit', [App\Http\Controllers\JobSeeker\CareerToolsController::class, 'submitAssessment'])
        ->name('job-seeker.career-tools.assessment.submit');
    Route::get('/job-seeker/career-tools/paths', [App\Http\Controllers\JobSeeker\CareerToolsController::class, 'careerPaths'])
        ->name('job-seeker.career-tools.paths');
    Route::get('/job-seeker/career-tools/documents', [App\Http\Controllers\JobSeeker\CareerToolsController::class, 'documents'])
        ->name('job-seeker.career-tools.documents');
    Route::get('/job-seeker/career-tools/documents/{id}/download', [App\Http\Controllers\JobSeeker\CareerToolsController::class, 'downloadDocument'])
        ->name('job-seeker.career-tools.documents.download');
    Route::delete('/job-seeker/career-tools/documents/{id}', [App\Http\Controllers\JobSeeker\CareerToolsController::class, 'destroyDocument'])
        ->name('job-seeker.career-tools.documents.destroy');

    Route::get('/job-seeker/support', [App\Http\Controllers\JobSeeker\SupportController::class, 'index'])
        ->name('job-seeker.support');
    Route::get('/job-seeker/support/bootstrap', [App\Http\Controllers\JobSeeker\SupportController::class, 'bootstrap'])
        ->name('job-seeker.support.bootstrap');
    Route::get('/job-seeker/support/tickets', [App\Http\Controllers\JobSeeker\SupportController::class, 'tickets'])
        ->name('job-seeker.support.tickets');
    Route::get('/job-seeker/support/tickets/{id}', [App\Http\Controllers\JobSeeker\SupportController::class, 'showTicket'])
        ->name('job-seeker.support.tickets.show');
    Route::post('/job-seeker/support/tickets', [App\Http\Controllers\JobSeeker\SupportController::class, 'storeTicket'])
        ->name('job-seeker.support.tickets.store');
    Route::post('/job-seeker/support/report-job', [App\Http\Controllers\JobSeeker\SupportController::class, 'reportJob'])
        ->name('job-seeker.support.report-job');

    Route::get('/job-seeker/settings', [App\Http\Controllers\JobSeeker\SettingsController::class, 'index'])
        ->name('job-seeker.settings');
    Route::get('/job-seeker/settings/bootstrap', [App\Http\Controllers\JobSeeker\SettingsController::class, 'bootstrap'])
        ->name('job-seeker.settings.bootstrap');
    Route::put('/job-seeker/settings/notifications', [App\Http\Controllers\JobSeeker\SettingsController::class, 'updateNotifications'])
        ->name('job-seeker.settings.notifications');
    Route::put('/job-seeker/settings/privacy', [App\Http\Controllers\JobSeeker\SettingsController::class, 'updatePrivacy'])
        ->name('job-seeker.settings.privacy');
    Route::put('/job-seeker/settings/password', [App\Http\Controllers\JobSeeker\SettingsController::class, 'changePassword'])
        ->name('job-seeker.settings.password');
    Route::put('/job-seeker/settings/two-factor', [App\Http\Controllers\JobSeeker\SettingsController::class, 'toggleTwoFactor'])
        ->name('job-seeker.settings.two-factor');
    
    // Employer Routes
    Route::get('/employer/dashboard', function () {
        return view('employer.dashboard');
    })->name('employer.dashboard');
    
    // Employer Job Postings
    Route::middleware('employer.permission:post_jobs')->group(function () {
        Route::get('/employer/jobs/create', [App\Http\Controllers\Employer\EmployerJobController::class, 'create'])->name('employer.jobs.create');
        Route::post('/employer/jobs', [App\Http\Controllers\Employer\EmployerJobController::class, 'store'])->name('employer.jobs.store');
    });
    Route::middleware('employer.permission:view_jobs,post_jobs')->group(function () {
        Route::get('/employer/jobs', [App\Http\Controllers\Employer\EmployerJobController::class, 'index'])->name('employer.jobs.index');
        Route::get('/employer/jobs/{id}', [App\Http\Controllers\Employer\EmployerJobController::class, 'show'])->name('employer.jobs.show');
        Route::get('/employer/jobs/{id}/stats', [App\Http\Controllers\Employer\EmployerJobController::class, 'statistics'])->name('employer.jobs.stats');
    });
    Route::middleware('employer.permission:post_jobs')->group(function () {
        Route::get('/employer/jobs/{id}/edit', [App\Http\Controllers\Employer\EmployerJobController::class, 'edit'])->name('employer.jobs.edit');
        Route::put('/employer/jobs/{id}', [App\Http\Controllers\Employer\EmployerJobController::class, 'update'])->name('employer.jobs.update');
        Route::post('/employer/jobs/{id}/toggle-status', [App\Http\Controllers\Employer\EmployerJobController::class, 'toggleStatus'])->name('employer.jobs.toggle-status');
        Route::delete('/employer/jobs/{id}', [App\Http\Controllers\Employer\EmployerJobController::class, 'destroy'])->name('employer.jobs.destroy');
    });

    // Employer Campaigns
    Route::middleware('employer.permission:manage_campaigns')->group(function () {
        Route::get('/employer/campaigns', [App\Http\Controllers\Employer\EmployerCampaignController::class, 'index'])->name('employer.campaigns.index');
        Route::get('/employer/campaigns/create', [App\Http\Controllers\Employer\EmployerCampaignController::class, 'create'])->name('employer.campaigns.create');
        Route::post('/employer/campaigns', [App\Http\Controllers\Employer\EmployerCampaignController::class, 'store'])->name('employer.campaigns.store');
        Route::post('/employer/campaigns/{id}/pause', [App\Http\Controllers\Employer\EmployerCampaignController::class, 'togglePause'])->name('employer.campaigns.toggle-pause');
        Route::post('/employer/campaigns/{id}/extend', [App\Http\Controllers\Employer\EmployerCampaignController::class, 'extend'])->name('employer.campaigns.extend');
        Route::post('/employer/campaigns/{id}/share', [App\Http\Controllers\Employer\EmployerCampaignController::class, 'share'])->name('employer.campaigns.share');
    });

    // Employer Coins & Billing + Invoices
    Route::middleware('employer.permission:billing')->group(function () {
        Route::get('/employer/coins', [App\Http\Controllers\Employer\EmployerCoinsController::class, 'index'])->name('employer.coins.index');
        Route::post('/employer/coins/purchase', [App\Http\Controllers\Employer\EmployerCoinsController::class, 'purchase'])->name('employer.coins.purchase');
        Route::get('/employer/invoices', [App\Http\Controllers\Employer\EmployerInvoiceController::class, 'index'])->name('employer.invoices.index');
        Route::get('/employer/invoices/export', [App\Http\Controllers\Employer\EmployerInvoiceController::class, 'export'])->name('employer.invoices.export');
        Route::get('/employer/invoices/{id}', [App\Http\Controllers\Employer\EmployerInvoiceController::class, 'show'])->name('employer.invoices.show');
        Route::get('/employer/invoices/{id}/download', [App\Http\Controllers\Employer\EmployerInvoiceController::class, 'download'])->name('employer.invoices.download');
        Route::post('/employer/invoices/payment-method', [App\Http\Controllers\Employer\EmployerInvoiceController::class, 'updatePaymentMethod'])->name('employer.invoices.payment-method');
    });

    // Employer Team Management
    Route::middleware('employer.permission')->group(function () {
        Route::get('/employer/team', [App\Http\Controllers\Employer\EmployerTeamController::class, 'index'])->name('employer.team.index');
        Route::post('/employer/team/invite', [App\Http\Controllers\Employer\EmployerTeamController::class, 'invite'])->name('employer.team.invite');
        Route::put('/employer/team/{id}/role', [App\Http\Controllers\Employer\EmployerTeamController::class, 'updateRole'])->name('employer.team.role');
        Route::post('/employer/team/{id}/role', [App\Http\Controllers\Employer\EmployerTeamController::class, 'updateRole']); // method spoof fallback
        Route::post('/employer/team/{id}/message', [App\Http\Controllers\Employer\EmployerTeamController::class, 'message'])->name('employer.team.message');
        Route::delete('/employer/team/{id}', [App\Http\Controllers\Employer\EmployerTeamController::class, 'destroy'])->name('employer.team.destroy');
        Route::post('/employer/team/{id}', [App\Http\Controllers\Employer\EmployerTeamController::class, 'destroy']); // method spoof fallback
    });
    
    // Employer Applications
    Route::middleware('employer.permission:view_applicants,review_applicants')->group(function () {
        Route::get('/employer/jobs/{id}/applicants', [App\Http\Controllers\Employer\EmployerApplicationController::class, 'jobApplicants'])->name('employer.jobs.applicants');
        Route::get('/employer/applications', [App\Http\Controllers\Employer\EmployerApplicationController::class, 'index'])->name('employer.applications.index');
        Route::get('/employer/applications/data', [App\Http\Controllers\Employer\EmployerApplicationController::class, 'getApplications'])->name('employer.applications.data');
        Route::get('/employer/applications/export', [App\Http\Controllers\Employer\EmployerApplicationController::class, 'export'])->name('employer.applications.export');
        Route::get('/employer/applications/{id}/pdf', [App\Http\Controllers\Employer\EmployerApplicationController::class, 'downloadPdf'])->name('employer.applications.pdf');
        Route::get('/employer/applications/{id}', [App\Http\Controllers\Employer\EmployerApplicationController::class, 'show'])->name('employer.applications.show');
        Route::get('/employer/jobs/{id}/talent-pool', [App\Http\Controllers\Employer\EmployerApplicationController::class, 'talentPoolForJob'])->name('employer.jobs.talent-pool');
    });
    Route::middleware('employer.permission:review_applicants')->group(function () {
        Route::put('/employer/applications/{id}/status', [App\Http\Controllers\Employer\EmployerApplicationController::class, 'updateStatus'])->name('employer.applications.update-status');
        Route::post('/employer/applications/{id}/talent-pool', [App\Http\Controllers\Employer\EmployerApplicationController::class, 'toggleTalentPool'])->name('employer.applications.talent-pool');
        Route::post('/employer/applications/{id}/invite', [App\Http\Controllers\Employer\EmployerApplicationController::class, 'inviteApplicant'])->name('employer.applications.invite');
    });
    Route::middleware('employer.permission:schedule_interviews,review_applicants')->group(function () {
        Route::post('/employer/applications/{id}/request-interview', [App\Http\Controllers\Employer\EmployerApplicationController::class, 'requestInterview'])->name('employer.applications.request-interview');
    });

    Route::middleware('employer.permission:view_analytics')->group(function () {
        Route::get('/employer/analytics', [App\Http\Controllers\Employer\EmployerAnalyticsController::class, 'index'])->name('employer.analytics.index');
        Route::get('/employer/analytics/export', [App\Http\Controllers\Employer\EmployerAnalyticsController::class, 'export'])->name('employer.analytics.export');
    });

    // Employer Tenders
    Route::get('/employer/tenders', [App\Http\Controllers\Employer\EmployerTenderController::class, 'index'])->name('employer.tenders.index');
    Route::post('/employer/tenders', [App\Http\Controllers\Employer\EmployerTenderController::class, 'store'])->name('employer.tenders.store');
    Route::post('/employer/tenders/upload-document', [App\Http\Controllers\Employer\EmployerTenderController::class, 'uploadDocument'])->name('employer.tenders.upload-document');
    Route::get('/employer/tenders/{id}', [App\Http\Controllers\Employer\EmployerTenderController::class, 'show'])->name('employer.tenders.show');
    Route::put('/employer/tenders/{id}', [App\Http\Controllers\Employer\EmployerTenderController::class, 'update'])->name('employer.tenders.update');
    Route::post('/employer/tenders/{id}/submit', [App\Http\Controllers\Employer\EmployerTenderController::class, 'submitForApproval'])->name('employer.tenders.submit');
    Route::delete('/employer/tenders/{id}', [App\Http\Controllers\Employer\EmployerTenderController::class, 'destroy'])->name('employer.tenders.destroy');

    // Employer Company Profile
    Route::middleware('employer.permission:company_settings')->group(function () {
        Route::get('/employer/company-profile', [App\Http\Controllers\Employer\EmployerCompanyProfileController::class, 'show'])->name('employer.company-profile');
        Route::post('/employer/company-profile', [App\Http\Controllers\Employer\EmployerCompanyProfileController::class, 'update'])->name('employer.company-profile.update');
        Route::post('/employer/company-profile/gallery', [App\Http\Controllers\Employer\EmployerCompanyProfileController::class, 'uploadGallery'])->name('employer.company-profile.upload-gallery');
        Route::delete('/employer/company-profile/gallery/{index}', [App\Http\Controllers\Employer\EmployerCompanyProfileController::class, 'deleteGalleryImage'])->name('employer.company-profile.delete-gallery');
    });
});

// Team invite accept (public token link)
Route::get('/team/invite/{token}', [App\Http\Controllers\Employer\EmployerTeamController::class, 'showInvite'])->name('team.invite.show');
Route::post('/team/invite/{token}', [App\Http\Controllers\Employer\EmployerTeamController::class, 'acceptInvite'])->name('team.invite.accept');
