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
    
    return redirect()->route('login');
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
    
    // Notifications
    Route::get('/api/notifications', [App\Http\Controllers\Api\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/api/notifications/unread-count', [App\Http\Controllers\Api\NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::put('/api/notifications/{id}/read', [App\Http\Controllers\Api\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::put('/api/notifications/mark-all-read', [App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    
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
    Route::get('/employer/jobs/{id}/edit', [App\Http\Controllers\Employer\EmployerJobController::class, 'edit'])->name('employer.jobs.edit');
    Route::put('/employer/jobs/{id}', [App\Http\Controllers\Employer\EmployerJobController::class, 'update'])->name('employer.jobs.update');
    Route::post('/employer/jobs/{id}/toggle-status', [App\Http\Controllers\Employer\EmployerJobController::class, 'toggleStatus'])->name('employer.jobs.toggle-status');
    Route::delete('/employer/jobs/{id}', [App\Http\Controllers\Employer\EmployerJobController::class, 'destroy'])->name('employer.jobs.destroy');
    
    // Employer Applications
    Route::get('/employer/applications', [App\Http\Controllers\Employer\EmployerApplicationController::class, 'index'])->name('employer.applications.index');
    Route::get('/employer/applications/data', [App\Http\Controllers\Employer\EmployerApplicationController::class, 'getApplications'])->name('employer.applications.data');
    Route::get('/employer/applications/export', [App\Http\Controllers\Employer\EmployerApplicationController::class, 'export'])->name('employer.applications.export');
    Route::get('/employer/applications/{id}', [App\Http\Controllers\Employer\EmployerApplicationController::class, 'show'])->name('employer.applications.show');
    Route::put('/employer/applications/{id}/status', [App\Http\Controllers\Employer\EmployerApplicationController::class, 'updateStatus'])->name('employer.applications.update-status');
    
    // Employer Company Profile
    Route::get('/employer/company-profile', [App\Http\Controllers\Employer\EmployerCompanyProfileController::class, 'show'])->name('employer.company-profile');
    Route::post('/employer/company-profile', [App\Http\Controllers\Employer\EmployerCompanyProfileController::class, 'update'])->name('employer.company-profile.update');
    Route::post('/employer/company-profile/gallery', [App\Http\Controllers\Employer\EmployerCompanyProfileController::class, 'uploadGallery'])->name('employer.company-profile.upload-gallery');
    Route::delete('/employer/company-profile/gallery/{index}', [App\Http\Controllers\Employer\EmployerCompanyProfileController::class, 'deleteGalleryImage'])->name('employer.company-profile.delete-gallery');
});
