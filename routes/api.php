<?php

use App\Http\Controllers\Admin\AdminAuthController;
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

// Public auth — Sanctum token only (no session I/O on mobile login path)
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/otp/send', [AuthController::class, 'sendOtp']);
Route::post('/auth/otp/verify', [AuthController::class, 'verifyOtp']);
Route::post('/auth/otp/resend', [AuthController::class, 'resendOtp']);
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

// Admin API (token-based, no session) — for external admin dashboard
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::middleware(['auth:sanctum', 'ensure.admin'])->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout']);
        Route::get('/me', [AdminAuthController::class, 'me']);
        Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index']);
        // Admin Management (users + roles)
        Route::get('/admin-users/overview', [\App\Http\Controllers\Admin\AdminUserController::class, 'overview']);
        Route::post('/admin-users', [\App\Http\Controllers\Admin\AdminUserController::class, 'store']);
        Route::put('/admin-users/{id}', [\App\Http\Controllers\Admin\AdminUserController::class, 'update']);
        Route::delete('/admin-users/{id}', [\App\Http\Controllers\Admin\AdminUserController::class, 'destroy']);
        Route::get('/job-seekers/overview', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'jobSeekersOverview']);
        Route::get('/employers/overview', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'employersOverview']);

        // Advertisements Management (job ads + tender ads overview)
        Route::get('/advertisements/overview', [\App\Http\Controllers\Admin\AdminAdvertisementsController::class, 'overview']);
        Route::get('/advertisements/all', [\App\Http\Controllers\Admin\AdminAdvertisementsController::class, 'all']);
        Route::get('/advertisements/job-campaigns', [\App\Http\Controllers\Admin\AdminAdvertisementsController::class, 'jobCampaigns']);
        Route::get('/advertisements/job-campaigns/dashboard', [\App\Http\Controllers\Admin\AdminAdvertisementsController::class, 'jobCampaignsDashboard']);
        // Manage single job ad (modal, delete), view applicants, edit, update, toggle status, share
        Route::get('/job-ads/{id}/manage', [\App\Http\Controllers\Admin\AdminJobAdController::class, 'manage']);
        Route::get('/job-ads/{id}/applicants', [\App\Http\Controllers\Admin\AdminJobAdController::class, 'applicants']);
        Route::get('/job-ads/{id}/edit', [\App\Http\Controllers\Admin\AdminJobAdController::class, 'editForm']);
        Route::put('/job-ads/{id}', [\App\Http\Controllers\Admin\AdminJobAdController::class, 'update']);
        Route::post('/job-ads/{id}/toggle-status', [\App\Http\Controllers\Admin\AdminJobAdController::class, 'toggleStatus']);
        Route::post('/job-ads/{id}/share', [\App\Http\Controllers\Admin\AdminJobAdController::class, 'share']);
        Route::delete('/job-ads/{id}', [\App\Http\Controllers\Admin\AdminJobAdController::class, 'destroy']);
        // Campaigns: extend, share (by campaign id)
        Route::post('/campaigns/{id}/extend', [\App\Http\Controllers\Admin\AdminCampaignController::class, 'extend']);
        Route::post('/campaigns/{id}/share', [\App\Http\Controllers\Admin\AdminCampaignController::class, 'share']);
        // Applications: invite applicant
        Route::post('/applications/{id}/invite', [\App\Http\Controllers\Admin\AdminApplicationController::class, 'inviteApplicant']);
        // Tender ads: dashboard (stats + list), view, approve, reject, request edits
        Route::get('/tenders/dashboard', [\App\Http\Controllers\Admin\AdminTenderController::class, 'dashboard']);
        Route::get('/tenders', [\App\Http\Controllers\Admin\AdminTenderController::class, 'index']);
        Route::get('/tenders/{id}', [\App\Http\Controllers\Admin\AdminTenderController::class, 'show']);
        Route::put('/tenders/{id}/approve', [\App\Http\Controllers\Admin\AdminTenderController::class, 'approve']);
        Route::put('/tenders/{id}/reject', [\App\Http\Controllers\Admin\AdminTenderController::class, 'reject']);
        Route::post('/tenders/{id}/request-edits', [\App\Http\Controllers\Admin\AdminTenderController::class, 'requestEdits']);

        // Financials Management (stats, revenue by category, transactions)
        Route::get('/financials/dashboard', [\App\Http\Controllers\Admin\AdminFinancialsController::class, 'dashboard']);
        Route::get('/financials/transactions', [\App\Http\Controllers\Admin\AdminFinancialsController::class, 'transactions']);

        // Refunds Management (dashboard, list, add, view, approve, reject, revert, reports)
        Route::get('/refunds/dashboard', [\App\Http\Controllers\Admin\AdminRefundsController::class, 'dashboard']);
        Route::get('/refunds/companies', [\App\Http\Controllers\Admin\AdminRefundsController::class, 'companies']);
        Route::get('/refunds', [\App\Http\Controllers\Admin\AdminRefundsController::class, 'index']);
        Route::post('/refunds', [\App\Http\Controllers\Admin\AdminRefundsController::class, 'store']);
        Route::get('/refunds/reports', [\App\Http\Controllers\Admin\AdminRefundsController::class, 'reports']);
        Route::get('/refunds/{id}', [\App\Http\Controllers\Admin\AdminRefundsController::class, 'show']);
        Route::put('/refunds/{id}/approve', [\App\Http\Controllers\Admin\AdminRefundsController::class, 'approve']);
        Route::put('/refunds/{id}/reject', [\App\Http\Controllers\Admin\AdminRefundsController::class, 'reject']);
        Route::put('/refunds/{id}/revert', [\App\Http\Controllers\Admin\AdminRefundsController::class, 'revert']);

        // Coin Management (dashboard, packages CRUD)
        Route::get('/coins/dashboard', [\App\Http\Controllers\Admin\AdminCoinsController::class, 'dashboard']);
        Route::get('/coins/packages', [\App\Http\Controllers\Admin\AdminCoinsController::class, 'index']);
        Route::post('/coins/packages', [\App\Http\Controllers\Admin\AdminCoinsController::class, 'store']);
        Route::get('/coins/packages/{id}', [\App\Http\Controllers\Admin\AdminCoinsController::class, 'show']);
        Route::put('/coins/packages/{id}', [\App\Http\Controllers\Admin\AdminCoinsController::class, 'update']);
        Route::delete('/coins/packages/{id}', [\App\Http\Controllers\Admin\AdminCoinsController::class, 'destroy']);
        Route::put('/coins/packages/{id}/toggle-status', [\App\Http\Controllers\Admin\AdminCoinsController::class, 'toggleStatus']);

        // Notification campaigns (management dashboard)
        Route::get('/notifications', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'index']);
        Route::post('/notifications', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'store']);
        Route::get('/notifications/{id}', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'show']);
        Route::put('/notifications/{id}', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'update']);
        Route::delete('/notifications/{id}', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'destroy']);
        Route::post('/notifications/{id}/duplicate', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'duplicate']);
        Route::post('/notifications/{id}/send', [\App\Http\Controllers\Admin\AdminNotificationController::class, 'send']);

        // Admin inbox (navbar bell – in-app notifications for admin)
        Route::get('/inbox', [\App\Http\Controllers\Admin\AdminInboxController::class, 'index']);
        Route::get('/inbox/unread-count', [\App\Http\Controllers\Admin\AdminInboxController::class, 'unreadCount']);
        Route::put('/inbox/mark-all-read', [\App\Http\Controllers\Admin\AdminInboxController::class, 'markAllAsRead']);
        Route::put('/inbox/{id}/read', [\App\Http\Controllers\Admin\AdminInboxController::class, 'markAsRead']);

        // Support tickets + job reports
        Route::get('/support-tickets', [\App\Http\Controllers\Admin\AdminSupportController::class, 'ticketsIndex']);
        Route::get('/support-tickets/{id}', [\App\Http\Controllers\Admin\AdminSupportController::class, 'ticketsShow']);
        Route::put('/support-tickets/{id}', [\App\Http\Controllers\Admin\AdminSupportController::class, 'ticketsUpdate']);
        Route::get('/job-reports', [\App\Http\Controllers\Admin\AdminSupportController::class, 'reportsIndex']);
        Route::put('/job-reports/{id}', [\App\Http\Controllers\Admin\AdminSupportController::class, 'reportsUpdate']);
    });
});

// Public job search (no auth required)
Route::get('/jobs/search', [\App\Http\Controllers\Public\JobSearchController::class, 'search']);
Route::get('/jobs/published', [\App\Http\Controllers\Public\JobSearchController::class, 'published']);
Route::get('/jobs/{id}', [\App\Http\Controllers\Public\JobSearchController::class, 'show']);

// Public companies and categories
Route::get('/companies/featured', [\App\Http\Controllers\Public\CompanyController::class, 'featured']);
Route::get('/public/companies', [\App\Http\Controllers\Public\CompanyController::class, 'apiIndex']);
Route::get('/public/companies/{idOrSlug}', [\App\Http\Controllers\Public\CompanyController::class, 'apiShow']);
Route::get('/public/companies/{idOrSlug}/jobs', [\App\Http\Controllers\Public\CompanyController::class, 'apiJobs']);
Route::get('/public/companies/{idOrSlug}/reviews', [\App\Http\Controllers\Public\CompanyController::class, 'apiReviews']);
Route::get('/companies/{id}', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'companyShow']);
Route::get('/companies/{id}/jobs', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'companyJobs']);
Route::get('/companies/{id}/reviews', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'companyReviews']);
Route::get('/categories/popular', [\App\Http\Controllers\Public\CategoryController::class, 'popular']);
Route::get('/categories', [\App\Http\Controllers\Public\CategoryController::class, 'index']);

// Public tenders (active tender ads only)
Route::get('/tenders', [\App\Http\Controllers\Public\TenderController::class, 'index']);
Route::get('/tenders/{id}/attachments/{attachmentId}/download', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'tenderAttachmentDownload']);
Route::get('/tenders/{id}/documents/zip', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'tenderDocumentsZip']);
Route::get('/tenders/{idOrSlug}', [\App\Http\Controllers\Public\TenderController::class, 'show']);

// Courses + meta (public)
Route::get('/courses', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'coursesIndex']);
Route::get('/courses/{id}', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'coursesShow']);
Route::get('/training-providers/featured', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'featuredProviders']);
Route::get('/meta/job-categories', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'metaJobCategories']);
Route::get('/meta/locations', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'metaLocations']);
Route::get('/meta/job-types', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'metaJobTypes']);
Route::get('/meta/education-levels', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'metaEducationLevels']);
Route::get('/meta/banners', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'metaBanners']);

// Authenticated Scoop/mobile routes — Bearer Sanctum only (no StartSession)
Route::middleware(['auth:sanctum'])->group(function () {
    // Authentication
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/change-password', [AuthController::class, 'changePassword']);
    Route::delete('/auth/account', [AuthController::class, 'deleteAccount']);
    Route::post('/auth/2fa/enable', [AuthController::class, 'enable2fa']);
    Route::post('/auth/2fa/disable', [AuthController::class, 'disable2fa']);

    // Scoop notifications (Bearer-friendly) + Blade PUT aliases
    Route::get('/notifications', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'notificationsIndex'])->name('notifications.index');
    Route::get('/notifications/unread-count', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'notificationsUnreadCount'])->name('notifications.unread-count');
    Route::patch('/notifications/read-all', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'notificationsMarkAllRead']);
    Route::put('/notifications/mark-all-read', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'notificationsMarkAllRead'])->name('notifications.mark-all-read');
    Route::patch('/notifications/{id}/read', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'notificationsMarkRead']);
    Route::put('/notifications/{id}/read', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'notificationsMarkRead'])->name('notifications.mark-read');
    Route::delete('/notifications/read', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'notificationsClearRead']);
    Route::delete('/notifications/{id}', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'notificationsDestroy']);

    // Job actions (Scoop aliases)
    Route::post('/jobs/{id}/apply', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'applyToJob']);
    Route::post('/jobs/{id}/save', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'saveJob']);
    Route::delete('/jobs/{id}/save', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'unsaveJob']);
    Route::post('/jobs/{id}/report', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'reportJob']);

    // Companies (auth)
    Route::post('/companies/{id}/reviews', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'companyReviewStore']);
    Route::post('/companies/{id}/follow', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'companyFollow']);
    Route::delete('/companies/{id}/follow', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'companyUnfollow']);

    // Tenders (auth actions)
    Route::post('/tenders/{id}/clarifications', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'tenderClarify']);
    Route::post('/tenders/{id}/report', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'tenderReport']);

    // Courses enroll stub
    Route::post('/courses/{id}/enroll', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'coursesEnroll']);

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
        Route::get('/summary', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'summary']);
        Route::get('/share-card', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'shareCard']);
        Route::get('/settings', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'getSettings']);
        Route::put('/settings', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'updateSettings']);
        Route::post('/contact-support', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'contactSupport']);

        // Scoop-shaped profile (also keep legacy controller responses via same paths with presenter in Scoop controller)
        Route::get('/profile', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'profile']);
        Route::put('/profile', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'updateProfile']);
        Route::post('/profile/photo', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'uploadPhoto']);

        Route::post('/profile/cv', [JobSeekerController::class, 'uploadCv']);
        Route::delete('/profile/cv', [JobSeekerController::class, 'deleteCv']);
        Route::get('/documents', [JobSeekerController::class, 'documents']);
        Route::post('/documents', [JobSeekerController::class, 'storeDocument']);
        Route::delete('/documents/{id}', [JobSeekerController::class, 'deleteDocument']);
        Route::get('/documents/{id}/download', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'downloadDocument']);
        Route::put('/documents/{id}/primary', [JobSeekerController::class, 'setPrimaryDocument']);
        Route::delete('/profile/photo', [JobSeekerController::class, 'deleteProfilePhoto']);
        Route::delete('/profile', [JobSeekerController::class, 'deleteProfile']);

        // Applications
        Route::get('/applications', [ApplicationController::class, 'index']);
        Route::get('/applications/check/{jobId}', [ApplicationController::class, 'check']);
        Route::get('/applications/{id}', [ApplicationController::class, 'show']);
        Route::post('/applications', [ApplicationController::class, 'store']);
        Route::delete('/applications/{id}/withdraw', [ApplicationController::class, 'withdraw']);

        // Saved / recommended / invitations
        Route::get('/saved-jobs', [\App\Http\Controllers\JobSeeker\SavedJobController::class, 'index']);
        Route::get('/saved-jobs/check/{jobId}', [\App\Http\Controllers\JobSeeker\SavedJobController::class, 'check']);
        Route::post('/saved-jobs', [\App\Http\Controllers\JobSeeker\SavedJobController::class, 'store']);
        Route::delete('/saved-jobs/{jobId}', [\App\Http\Controllers\JobSeeker\SavedJobController::class, 'destroy']);
        Route::get('/recommended-jobs', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'recommendedJobs']);
        Route::get('/invitations', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'invitations']);
        Route::post('/invitations/{id}/accept', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'acceptInvitation']);
        Route::post('/invitations/{id}/decline', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'declineInvitation']);

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

        // Hobbies + social links
        Route::get('/hobbies', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'hobbiesIndex']);
        Route::post('/hobbies', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'hobbiesStore']);
        Route::delete('/hobbies/{id}', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'hobbiesDestroy']);
        Route::get('/social-links', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'socialLinksIndex']);
        Route::post('/social-links', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'socialLinksStore']);
        Route::delete('/social-links/{id}', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'socialLinksDestroy']);

        // Certifications
        Route::get('/certifications', [\App\Http\Controllers\JobSeeker\CertificationController::class, 'index']);
        Route::post('/certifications', [\App\Http\Controllers\JobSeeker\CertificationController::class, 'store']);
        Route::put('/certifications/{id}', [\App\Http\Controllers\JobSeeker\CertificationController::class, 'update']);
        Route::delete('/certifications/{id}', [\App\Http\Controllers\JobSeeker\CertificationController::class, 'destroy']);
        Route::post('/certifications/{id}/document', [\App\Http\Controllers\Api\Scoop\ScoopApiController::class, 'uploadCertificationDocument']);

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
