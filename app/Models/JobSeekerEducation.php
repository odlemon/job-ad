<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobSeekerEducation extends Model
{
    use SoftDeletes;

    protected $table = 'job_seeker_educations';

    protected $fillable = [
        'seeker_id',
        'degree',
        'institution',
        'location',
        'start_date',
        'end_date',
        'gpa',
        'gpa_scale',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'gpa' => 'decimal:2',
    ];

    public function jobSeeker(): BelongsTo
    {
        return $this->belongsTo(JobSeeker::class, 'seeker_id', 'seeker_id');
    }
}
