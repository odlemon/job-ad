<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class NotificationService
{
    /**
     * Create a new notification.
     */
    public function create(array $data): Notification
    {
        return Notification::create($data);
    }

    /**
     * Create a notification for application received (for employer).
     */
    public function notifyApplicationReceived(int $employerUserId, int $applicationId, string $jobTitle, string $applicantName): Notification
    {
        return $this->create([
            'user_id' => $employerUserId,
            'type' => 'application_received',
            'title' => 'New Application Received',
            'message' => "{$applicantName} applied for {$jobTitle}",
            'data' => [
                'application_id' => $applicationId,
                'type' => 'application_received',
            ],
        ]);
    }

    /**
     * Create a notification for status update (for job seeker).
     */
    public function notifyStatusUpdate(int $jobSeekerUserId, int $applicationId, string $status, string $jobTitle, string $companyName): Notification
    {
        $statusMessages = [
            'pending' => 'Your application is pending review',
            'reviewing' => 'Your application is being reviewed',
            'shortlisted' => 'Congratulations! You have been shortlisted',
            'hired' => 'Congratulations! You have been hired',
            'rejected' => 'Your application was not selected',
        ];

        $statusText = match($status) {
            'pending' => 'moved to Pending stage',
            'reviewing' => 'moved to Reviewing stage',
            'shortlisted' => 'moved to Shortlisted stage',
            'hired' => 'moved to Hired stage',
            'rejected' => 'moved to Rejected stage',
            default => 'status has been updated',
        };
        
        $title = 'Application Status Updated';
        $message = "Your application for {$jobTitle} at {$companyName} has been {$statusText}";

        return $this->create([
            'user_id' => $jobSeekerUserId,
            'type' => 'status_updated',
            'title' => $title,
            'message' => $message,
            'data' => [
                'application_id' => $applicationId,
                'status' => $status,
                'job_title' => $jobTitle,
                'company_name' => $companyName,
                'type' => 'status_updated',
            ],
        ]);
    }

    /**
     * Get unread notifications for a user.
     */
    public function getUnreadNotifications(int $userId, int $limit = 10): Collection
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get all notifications for a user.
     */
    public function getAllNotifications(int $userId, int $limit = 20): Collection
    {
        return Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get unread count for a user.
     */
    public function getUnreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(int $notificationId, int $userId): bool
    {
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();

        if (!$notification) {
            return false;
        }

        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return true;
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Notify job seekers about new job in their industry/category.
     */
    public function notifyNewJob(int $jobId, string $jobTitle, string $companyName, ?int $categoryId = null): void
    {
        $jobSeekers = collect();

        // Get job seekers who have category preferences matching this job's category
        if ($categoryId) {
            $matchingSeekers = \App\Models\JobSeeker::whereHas('categoryPreferences', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->where('open_to_opportunities', true)
            ->with('user')
            ->get();
            
            $jobSeekers = $jobSeekers->merge($matchingSeekers);
        }

        // Also get job seekers with open_to_opportunities = true (general notification)
        $generalSeekers = \App\Models\JobSeeker::where('open_to_opportunities', true)
            ->with('user')
            ->get();
        
        // Merge and remove duplicates
        $jobSeekers = $jobSeekers->merge($generalSeekers)->unique('seeker_id');

        // Create notifications for each job seeker
        foreach ($jobSeekers as $seeker) {
            if ($seeker->user && $seeker->user->id) {
                $this->create([
                    'user_id' => $seeker->user->id,
                    'type' => 'new_job_alert',
                    'title' => 'New Job Alert',
                    'message' => "3 new jobs matching your preferences: {$jobTitle}",
                    'data' => [
                        'job_id' => $jobId,
                        'job_title' => $jobTitle,
                        'company_name' => $companyName,
                        'type' => 'new_job_alert',
                    ],
                ]);
            }
        }
    }
}
