<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobAdvertisement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'category_id',
        'title',
        'slug',
        'description',
        'requirements',
        'benefits',
        'employment_type',
        'experience_level',
        'salary_min',
        'salary_max',
        'currency',
        'hide_salary',
        'location',
        'island',
        'district',
        'is_remote',
        'work_environment',
        'education_level',
        'views_count',
        'applications_count',
        'status',
        'published_at',
        'application_questions',
    ];

    protected $casts = [
        'is_remote' => 'boolean',
        'hide_salary' => 'boolean',
        'published_at' => 'datetime',
        'views_count' => 'integer',
        'applications_count' => 'integer',
        'application_questions' => 'array',
    ];

    /**
     * Get the company that owns the job advertisement.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the category that owns the job advertisement.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class, 'category_id');
    }

    /**
     * Get the job applications for the job advertisement.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'job_advertisement_id');
    }

    /**
     * Get the campaigns for the job advertisement.
     */
    public function campaigns(): HasMany
    {
        return $this->hasMany(JobCampaign::class);
    }

    /**
     * Get the saved-job records (job seekers who saved this job).
     */
    public function savedJobs(): HasMany
    {
        return $this->hasMany(SavedJob::class, 'job_id');
    }

    /**
     * Get the share records (job seekers who shared this job).
     */
    public function jobShares(): HasMany
    {
        return $this->hasMany(JobShare::class, 'job_id');
    }
}
