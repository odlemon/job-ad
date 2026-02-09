<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowedCompany extends Model
{
    protected $primaryKey = 'follow_id';

    protected $fillable = [
        'seeker_id',
        'company_id',
        'followed_at',
    ];

    protected $casts = [
        'followed_at' => 'datetime',
    ];

    /**
     * Get the job seeker that follows the company.
     */
    public function jobSeeker(): BelongsTo
    {
        return $this->belongsTo(JobSeeker::class, 'seeker_id', 'seeker_id');
    }

    /**
     * Get the company that is being followed.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
