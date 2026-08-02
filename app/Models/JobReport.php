<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobReport extends Model
{
    protected $fillable = [
        'user_id',
        'job_advertisement_id',
        'category',
        'reason',
        'details',
        'status',
        'admin_notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobAdvertisement(): BelongsTo
    {
        return $this->belongsTo(JobAdvertisement::class, 'job_advertisement_id');
    }
}
