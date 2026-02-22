<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\JobSeeker\ApplicationController;
use App\Http\Controllers\JobSeeker\JobSeekerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Middleware stack for session-based API auth (must match web middleware for cookie compatibility)
$sessionMiddleware = [
    \Illuminate\Cookie\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
];

// Public routes (with session middleware for auth)
Route::middleware($sessionMiddleware)->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
});

// Public job search (no auth required)
Route::get('/jobs/search', [\App\Http\Controllers\Public\JobSearchController::class, 'search']);
Route::get('/jobs/published', [\App\Http\Controllers\Public\JobSearchController::class, 'published']);
Route::get('/jobs/{id}', [\App\Http\Controllers\Public\JobSearchController::class, 'show']);

// Public companies and categories
Route::get('/companies/featured', [\App\Http\Controllers\Public\CompanyController::class, 'featured']);
Route::get('/categories/popular', [\App\Http\Controllers\Public\CategoryController::class, 'popular']);
Route::get('/categories', [\App\Http\Controllers\Public\CategoryController::class, 'index']);

// Authenticated routes (using session auth)
Route::middleware(array_merge($sessionMiddleware, ['auth']))->group(function () {
    // Authentication
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // File Upload routes
    Route::post('/upload', [\App\Http\Controllers\FileController::class, 'uploadFile']);
    Route::post('/upload-multiple', [\App\Http\Controllers\FileController::class, 'uploadFiles']);

    // Employer routes
    Route::prefix('employer')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Employer\EmployerDashboardController::class, 'index']);
    });

    // Job Seeker routes
    Route::prefix('job-seeker')->middleware(['ensure.job.seeker'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\JobSeeker\DashboardController::class, 'index']);
        
        // Profile management
        Route::get('/profile', [JobSeekerController::class, 'profile']);
        Route::put('/profile', [JobSeekerController::class, 'updateProfile']);
        Route::post('/profile/cv', [JobSeekerController::class, 'uploadCv']);
        Route::delete('/profile/cv', [JobSeekerController::class, 'deleteCv']);
        Route::post('/profile/photo', [JobSeekerController::class, 'uploadProfilePhoto']);
        Route::delete('/profile/photo', [JobSeekerController::class, 'deleteProfilePhoto']);
        Route::delete('/profile', [JobSeekerController::class, 'deleteProfile']);

        // Applications
        Route::get('/applications', [ApplicationController::class, 'index']);
        Route::get('/applications/check/{jobId}', [ApplicationController::class, 'check']);
        Route::get('/applications/{id}', [ApplicationController::class, 'show']);
        Route::post('/applications', [ApplicationController::class, 'store']);
        Route::delete('/applications/{id}/withdraw', [ApplicationController::class, 'withdraw']);

        // Saved Jobs
        Route::get('/saved-jobs', [\App\Http\Controllers\JobSeeker\SavedJobController::class, 'index']);
        Route::get('/saved-jobs/check/{jobId}', [\App\Http\Controllers\JobSeeker\SavedJobController::class, 'check']);
        Route::post('/saved-jobs', [\App\Http\Controllers\JobSeeker\SavedJobController::class, 'store']);
        Route::delete('/saved-jobs/{jobId}', [\App\Http\Controllers\JobSeeker\SavedJobController::class, 'destroy']);

        // Followed Companies
        Route::get('/followed-companies', [\App\Http\Controllers\JobSeeker\FollowedCompanyController::class, 'index']);
        Route::get('/followed-companies/check/{companyId}', [\App\Http\Controllers\JobSeeker\FollowedCompanyController::class, 'check']);
        Route::post('/followed-companies', [\App\Http\Controllers\JobSeeker\FollowedCompanyController::class, 'store']);
        Route::delete('/followed-companies/{companyId}', [\App\Http\Controllers\JobSeeker\FollowedCompanyController::class, 'destroy']);

        // Work Experience
        Route::get('/experiences', [\App\Http\Controllers\JobSeeker\ExperienceController::class, 'index']);
        Route::post('/experiences', [\App\Http\Controllers\JobSeeker\ExperienceController::class, 'store']);
        Route::put('/experiences/{id}', [\App\Http\Controllers\JobSeeker\ExperienceController::class, 'update']);
        Route::delete('/experiences/{id}', [\App\Http\Controllers\JobSeeker\ExperienceController::class, 'destroy']);

        // Education
        Route::get('/educations', [\App\Http\Controllers\JobSeeker\EducationController::class, 'index']);
        Route::post('/educations', [\App\Http\Controllers\JobSeeker\EducationController::class, 'store']);
        Route::put('/educations/{id}', [\App\Http\Controllers\JobSeeker\EducationController::class, 'update']);
        Route::delete('/educations/{id}', [\App\Http\Controllers\JobSeeker\EducationController::class, 'destroy']);

        // Skills
        Route::get('/skills', [\App\Http\Controllers\JobSeeker\SkillController::class, 'index']);
        Route::post('/skills', [\App\Http\Controllers\JobSeeker\SkillController::class, 'store']);
        Route::put('/skills/{id}', [\App\Http\Controllers\JobSeeker\SkillController::class, 'update']);
        Route::delete('/skills/{id}', [\App\Http\Controllers\JobSeeker\SkillController::class, 'destroy']);

        // Languages
        Route::get('/languages', [\App\Http\Controllers\JobSeeker\LanguageController::class, 'index']);
        Route::post('/languages', [\App\Http\Controllers\JobSeeker\LanguageController::class, 'store']);
        Route::put('/languages/{id}', [\App\Http\Controllers\JobSeeker\LanguageController::class, 'update']);
        Route::delete('/languages/{id}', [\App\Http\Controllers\JobSeeker\LanguageController::class, 'destroy']);

        // Certifications
        Route::get('/certifications', [\App\Http\Controllers\JobSeeker\CertificationController::class, 'index']);
        Route::post('/certifications', [\App\Http\Controllers\JobSeeker\CertificationController::class, 'store']);
        Route::put('/certifications/{id}', [\App\Http\Controllers\JobSeeker\CertificationController::class, 'update']);
        Route::delete('/certifications/{id}', [\App\Http\Controllers\JobSeeker\CertificationController::class, 'destroy']);

        // References
        Route::get('/references', [\App\Http\Controllers\JobSeeker\ReferenceController::class, 'index']);
        Route::post('/references', [\App\Http\Controllers\JobSeeker\ReferenceController::class, 'store']);
        Route::put('/references/{id}', [\App\Http\Controllers\JobSeeker\ReferenceController::class, 'update']);
        Route::delete('/references/{id}', [\App\Http\Controllers\JobSeeker\ReferenceController::class, 'destroy']);

        // Category Preferences
        Route::get('/category-preferences', [\App\Http\Controllers\JobSeeker\CategoryPreferenceController::class, 'index']);
        Route::post('/category-preferences', [\App\Http\Controllers\JobSeeker\CategoryPreferenceController::class, 'store']);
        Route::post('/category-preferences/sync', [\App\Http\Controllers\JobSeeker\CategoryPreferenceController::class, 'sync']);
        Route::delete('/category-preferences/{categoryId}', [\App\Http\Controllers\JobSeeker\CategoryPreferenceController::class, 'destroy']);
    });
});
