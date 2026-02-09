<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'job_advertisement_id',
        'user_id',
        'seeker_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'cover_letter',
        'resume_path',
        'additional_info',
        'status',
        'notes',
        'employer_notes',
        'reviewed_at',
        'reviewed_by',
    ];

    protected $casts = [
        'additional_info' => 'array',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the job advertisement that owns the application.
     */
    public function jobAdvertisement(): BelongsTo
    {
        return $this->belongsTo(JobAdvertisement::class, 'job_advertisement_id');
    }

    /**
     * Get the user that owns the application.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the job seeker that submitted the application.
     */
    public function jobSeeker(): BelongsTo
    {
        return $this->belongsTo(JobSeeker::class, 'seeker_id', 'seeker_id');
    }

    /**
     * Get the user who reviewed the application.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
