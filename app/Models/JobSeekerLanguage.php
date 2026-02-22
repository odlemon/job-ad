<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobSeekerLanguage extends Model
{
    protected $table = 'job_seeker_languages';

    protected $fillable = [
        'seeker_id',
        'language',
        'proficiency_level',
    ];

    public function jobSeeker(): BelongsTo
    {
        return $this->belongsTo(JobSeeker::class, 'seeker_id', 'seeker_id');
    }
}
