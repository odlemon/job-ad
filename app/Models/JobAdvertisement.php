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
        'location',
        'is_remote',
        'application_deadline',
        'views_count',
        'applications_count',
        'status',
        'published_at',
    ];

    protected $casts = [
        'is_remote' => 'boolean',
        'application_deadline' => 'date',
        'published_at' => 'datetime',
        'views_count' => 'integer',
        'applications_count' => 'integer',
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
}
