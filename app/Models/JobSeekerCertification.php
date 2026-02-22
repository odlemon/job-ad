<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobSeekerCertification extends Model
{
    use SoftDeletes;

    protected $table = 'job_seeker_certifications';

    protected $fillable = [
        'seeker_id',
        'certification_name',
        'issuing_organization',
        'issue_date',
        'expiry_date',
        'certificate_file_path',
        'credential_id',
        'credential_url',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function jobSeeker(): BelongsTo
    {
        return $this->belongsTo(JobSeeker::class, 'seeker_id', 'seeker_id');
    }
}
