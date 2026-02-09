<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedJob extends Model
{
    protected $primaryKey = 'saved_id';

    protected $fillable = [
        'seeker_id',
        'job_id',
        'saved_at',
    ];

    protected $casts = [
        'saved_at' => 'datetime',
    ];

    /**
     * Get the job seeker that saved the job.
     */
    public function jobSeeker(): BelongsTo
    {
        return $this->belongsTo(JobSeeker::class, 'seeker_id', 'seeker_id');
    }

    /**
     * Get the job that was saved.
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(JobAdvertisement::class, 'job_id');
    }
}
