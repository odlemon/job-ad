<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobSeekerSkill extends Model
{
    protected $table = 'job_seeker_skills';

    protected $fillable = [
        'seeker_id',
        'skill_name',
        'proficiency_level',
    ];

    public function jobSeeker(): BelongsTo
    {
        return $this->belongsTo(JobSeeker::class, 'seeker_id', 'seeker_id');
    }
}
