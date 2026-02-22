<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobSeekerReference extends Model
{
    use SoftDeletes;

    protected $table = 'job_seeker_references';

    protected $fillable = [
        'seeker_id',
        'reference_name',
        'title',
        'company',
        'relationship',
        'email',
        'phone',
        'notes',
    ];

    public function jobSeeker(): BelongsTo
    {
        return $this->belongsTo(JobSeeker::class, 'seeker_id', 'seeker_id');
    }
}
