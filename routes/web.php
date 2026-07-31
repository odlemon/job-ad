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
    
    // Notifications API moved to routes/api.php (Sanctum + session) for Scoop mobile + Blade
    
    Route::get('/job-seeker/saved-jobs', function () {
        return view('job-seeker.saved-jobs');
    })->name('job-seeker.saved-jobs');
    
    Route::get('/job-seeker/followed-companies', function () {
        return view('job-seeker.followed-companies');
    })->name('job-seeker.followed-companies');
    
    // Employer Routes
    Route::get('/employer/dashboard', function () {
        return view('employer.dashboard');
    })->name('employer.dashboard');
    
    // Employer Job Postings
    Route::get('/employer/jobs', [App\Http\Controllers\Employer\EmployerJobController::class, 'index'])->name('employer.jobs.index');
    Route::get('/employer/jobs/create', [App\Http\Controllers\Employer\EmployerJobController::class, 'create'])->name('employer.jobs.create');
    Route::post('/employer/jobs', [App\Http\Controllers\Employer\EmployerJobController::class, 'store'])->name('employer.jobs.store');
    Route::get('/employer/jobs/{id}', [App\Http\Controllers\Employer\EmployerJobController::class, 'show'])->name('employer.jobs.show');
    Route::get('/employer/jobs/{id}/stats', [App\Http\Controllers\Employer\EmployerJobController::class, 'statistics'])->name('employer.jobs.stats');
    Route::get('/employer/jobs/{id}/edit', [App\Http\Controllers\Employer\EmployerJobController::class, 'edit'])->name('employer.jobs.edit');
    Route::put('/employer/jobs/{id}', [App\Http\Controllers\Employer\EmployerJobController::class, 'update'])->name('employer.jobs.update');
    Route::post('/employer/jobs/{id}/toggle-status', [App\Http\Controllers\Employer\EmployerJobController::class, 'toggleStatus'])->name('employer.jobs.toggle-status');
    Route::delete('/employer/jobs/{id}', [App\Http\Controllers\Employer\EmployerJobController::class, 'destroy'])->name('employer.jobs.destroy');

    // Employer Campaigns
    Route::get('/employer/campaigns', [App\Http\Controllers\Employer\EmployerCampaignController::class, 'index'])->name('employer.campaigns.index');
    Route::get('/employer/campaigns/create', [App\Http\Controllers\Employer\EmployerCampaignController::class, 'create'])->name('employer.campaigns.create');
    Route::post('/employer/campaigns', [App\Http\Controllers\Employer\EmployerCampaignController::class, 'store'])->name('employer.campaigns.store');
    Route::post('/employer/campaigns/{id}/pause', [App\Http\Controllers\Employer\EmployerCampaignController::class, 'togglePause'])->name('employer.campaigns.toggle-pause');
    Route::post('/employer/campaigns/{id}/extend', [App\Http\Controllers\Employer\EmployerCampaignController::class, 'extend'])->name('employer.campaigns.extend');
    Route::post('/employer/campaigns/{id}/share', [App\Http\Controllers\Employer\EmployerCampaignController::class, 'share'])->name('employer.campaigns.share');
    
    // Employer Applications
    Route::get('/employer/jobs/{id}/applicants', [App\Http\Controllers\Employer\EmployerApplicationController::class, 'jobApplicants'])->name('employer.jobs.applicants');
    Route::get('/employer/applications', [App\Http\Controllers\Employer\EmployerApplicationController::class, 'index'])->name('employer.applications.index');
    Route::get('/employer/applications/data', [App\Http\Controllers\Employer\EmployerApplicationController::class, 'getApplications'])->name('employer.applications.data');
    Route::get('/employer/applications/export', [App\Http\Controllers\Employer\EmployerApplicationController::class, 'export'])->name('employer.applications.export');
    Route::get('/employer/applications/{id}/pdf', [App\Http\Controllers\Employer\EmployerApplicationController::class, 'downloadPdf'])->name('employer.applications.pdf');
    Route::get('/employer/applications/{id}', [App\Http\Controllers\Employer\EmployerApplicationController::class, 'show'])->name('employer.applications.show');
    Route::put('/employer/applications/{id}/status', [App\Http\Controllers\Employer\EmployerApplicationController::class, 'updateStatus'])->name('employer.applications.update-status');
    Route::post('/employer/applications/{id}/request-interview', [App\Http\Controllers\Employer\EmployerApplicationController::class, 'requestInterview'])->name('employer.applications.request-interview');
    Route::post('/employer/applications/{id}/talent-pool', [App\Http\Controllers\Employer\EmployerApplicationController::class, 'toggleTalentPool'])->name('employer.applications.talent-pool');
    Route::post('/employer/applications/{id}/invite', [App\Http\Controllers\Employer\EmployerApplicationController::class, 'inviteApplicant'])->name('employer.applications.invite');
    Route::get('/employer/jobs/{id}/talent-pool', [App\Http\Controllers\Employer\EmployerApplicationController::class, 'talentPoolForJob'])->name('employer.jobs.talent-pool');

    // Employer Tenders
    Route::get('/employer/tenders', [App\Http\Controllers\Employer\EmployerTenderController::class, 'index'])->name('employer.tenders.index');
    Route::post('/employer/tenders', [App\Http\Controllers\Employer\EmployerTenderController::class, 'store'])->name('employer.tenders.store');
    Route::post('/employer/tenders/upload-document', [App\Http\Controllers\Employer\EmployerTenderController::class, 'uploadDocument'])->name('employer.tenders.upload-document');
    Route::get('/employer/tenders/{id}', [App\Http\Controllers\Employer\EmployerTenderController::class, 'show'])->name('employer.tenders.show');
    Route::put('/employer/tenders/{id}', [App\Http\Controllers\Employer\EmployerTenderController::class, 'update'])->name('employer.tenders.update');
    Route::post('/employer/tenders/{id}/submit', [App\Http\Controllers\Employer\EmployerTenderController::class, 'submitForApproval'])->name('employer.tenders.submit');
    Route::delete('/employer/tenders/{id}', [App\Http\Controllers\Employer\EmployerTenderController::class, 'destroy'])->name('employer.tenders.destroy');

    // Employer Company Profile
    Route::get('/employer/company-profile', [App\Http\Controllers\Employer\EmployerCompanyProfileController::class, 'show'])->name('employer.company-profile');
    Route::post('/employer/company-profile', [App\Http\Controllers\Employer\EmployerCompanyProfileController::class, 'update'])->name('employer.company-profile.update');
    Route::post('/employer/company-profile/gallery', [App\Http\Controllers\Employer\EmployerCompanyProfileController::class, 'uploadGallery'])->name('employer.company-profile.upload-gallery');
    Route::delete('/employer/company-profile/gallery/{index}', [App\Http\Controllers\Employer\EmployerCompanyProfileController::class, 'deleteGalleryImage'])->name('employer.company-profile.delete-gallery');
});
