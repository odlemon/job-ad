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
        $notification = Notification::create($data);

        $type = (string) ($notification->type ?? '');
        $category = str_contains($type, 'application') || str_contains($type, 'interview') || str_contains($type, 'status')
            ? 'applications'
            : 'alerts';

        app(RealtimeBroadcaster::class)->emitToUser((int) $notification->user_id, 'notification:new', [
            'id' => $notification->id,
            'title' => $notification->title,
            'body' => $notification->message,
            'message' => $notification->message,
            'type' => $notification->type,
            'category' => $category,
            'is_read' => (bool) $notification->is_read,
            'read' => (bool) $notification->is_read,
            'data' => $notification->data,
            'created_at' => optional($notification->created_at)?->toIso8601String(),
            'date' => optional($notification->created_at)?->diffForHumans(),
        ]);

        return $notification;
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
     * Notify job seeker that they have been requested for an interview.
     */
    public function notifyInterviewRequested(
        int $jobSeekerUserId,
        int $applicationId,
        string $jobTitle,
        string $companyName,
        ?\DateTimeInterface $scheduledAt = null,
        ?string $location = null,
        ?string $notes = null
    ): Notification {
        $parts = ["{$companyName} has requested an interview for the position: {$jobTitle}."];
        if ($scheduledAt) {
            $parts[] = 'Scheduled: ' . $scheduledAt->format('l, F j, Y \a\t g:i A');
        }
        if ($location) {
            $parts[] = 'Location: ' . $location;
        }
        if ($notes) {
            $parts[] = 'Notes: ' . $notes;
        }
        $message = implode(' ', $parts);

        return $this->create([
            'user_id' => $jobSeekerUserId,
            'type' => 'interview_requested',
            'title' => 'Interview Request',
            'message' => $message,
            'data' => [
                'application_id' => $applicationId,
                'job_title' => $jobTitle,
                'company_name' => $companyName,
                'scheduled_at' => $scheduledAt?->format('c'),
                'location' => $location,
                'notes' => $notes,
                'type' => 'interview_requested',
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
     * Paginate notifications with optional filters in SQL.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginateForUser(int $userId, int $perPage = 20, ?bool $isRead = null, ?string $category = null)
    {
        $query = Notification::where('user_id', $userId)->orderByDesc('created_at');

        if ($isRead !== null) {
            $query->where('is_read', $isRead);
        }

        if ($category === 'applications') {
            $query->where(function ($q) {
                $q->where('type', 'like', '%application%')
                    ->orWhere('type', 'like', '%interview%')
                    ->orWhere('type', 'like', '%status%');
            });
        } elseif ($category === 'alerts') {
            $query->where(function ($q) {
                $q->where('type', 'not like', '%application%')
                    ->where('type', 'not like', '%interview%')
                    ->where('type', 'not like', '%status%');
            });
        }

        return $query->paginate($perPage);
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
     * Notify all admin users (for navbar / inbox). Used when: new employer, new job seeker, new job post, someone hired.
     */
    public function notifyAdmins(string $type, string $title, string $message, array $data = []): void
    {
        $adminIds = User::where('user_type', 'admin')->pluck('id');
        foreach ($adminIds as $adminId) {
            $this->create([
                'user_id' => $adminId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => array_merge($data, ['type' => $type]),
            ]);
        }
    }

    /**
     * Notify job seekers about new job in their industry/category.
     * Runs after the HTTP response so publishing stays fast; only notifies
     * seekers who match the job category (avoids loading every open seeker).
     */
    public function notifyNewJob(int $jobId, string $jobTitle, string $companyName, ?int $categoryId = null): void
    {
        dispatch(function () use ($jobId, $jobTitle, $companyName, $categoryId) {
            if (! $categoryId) {
                return;
            }

            \App\Models\JobSeeker::query()
                ->where('open_to_opportunities', true)
                ->whereHas('categoryPreferences', function ($query) use ($categoryId) {
                    $query->where('category_id', $categoryId);
                })
                ->with('user:id')
                ->orderBy('seeker_id')
                ->chunk(100, function ($seekers) use ($jobId, $jobTitle, $companyName) {
                    foreach ($seekers as $seeker) {
                        if (! $seeker->user?->id) {
                            continue;
                        }
                        $this->create([
                            'user_id' => $seeker->user->id,
                            'type' => 'new_job_alert',
                            'title' => 'New Job Alert',
                            'message' => "New job matching your preferences: {$jobTitle}",
                            'data' => [
                                'job_id' => $jobId,
                                'job_title' => $jobTitle,
                                'company_name' => $companyName,
                                'type' => 'new_job_alert',
                            ],
                        ]);
                    }
                });
        })->afterResponse();
    }
}
