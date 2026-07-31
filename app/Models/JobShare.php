<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobShare extends Model
{
    protected $fillable = [
        'job_id',
        'seeker_id',
        'platform',
        'shared_at',
    ];

    protected $casts = [
        'shared_at' => 'datetime',
    ];

    public function jobAdvertisement(): BelongsTo
    {
        return $this->belongsTo(JobAdvertisement::class, 'job_id');
    }

    public function jobSeeker(): BelongsTo
    {
        return $this->belongsTo(JobSeeker::class, 'seeker_id', 'seeker_id');
    }
}
