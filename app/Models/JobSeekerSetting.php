<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobSeekerSetting extends Model
{
    protected $fillable = [
        'seeker_id',
        'app_notifications',
        'email_notifications',
        'job_alerts',
        'application_updates',
        'marketing_emails',
        'two_factor_enabled',
        'show_activity_status',
        'allow_contact_by_recruiters',
    ];

    protected $casts = [
        'app_notifications' => 'boolean',
        'email_notifications' => 'boolean',
        'job_alerts' => 'boolean',
        'application_updates' => 'boolean',
        'marketing_emails' => 'boolean',
        'two_factor_enabled' => 'boolean',
        'show_activity_status' => 'boolean',
        'allow_contact_by_recruiters' => 'boolean',
    ];

    public function jobSeeker(): BelongsTo
    {
        return $this->belongsTo(JobSeeker::class, 'seeker_id', 'seeker_id');
    }
}
