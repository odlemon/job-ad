<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobSeeker extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'seeker_id';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'profile_photo',
        'bio',
        'location',
        'phone',
        'address',
        'gender',
        'date_of_birth',
        'employment_status',
        'highest_education',
        'driving_license',
        'license_issued_date',
        'job_preferences',
        'linkedin_url',
        'website_url',
        'public_profile',
        'open_to_opportunities',
        'hobbies',
        'cv_file_path',
        'cv_uploaded_at',
    ];

    protected $casts = [
        'cv_uploaded_at' => 'datetime',
        'date_of_birth' => 'date',
        'license_issued_date' => 'date',
        'driving_license' => 'boolean',
        'public_profile' => 'boolean',
        'open_to_opportunities' => 'boolean',
        'job_preferences' => 'array',
        'hobbies' => 'array',
    ];

    /**
     * Get the user that owns the job seeker profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the applications for the job seeker.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'seeker_id', 'seeker_id');
    }

    /**
     * Get the saved jobs for the job seeker.
     */
    public function savedJobs(): HasMany
    {
        return $this->hasMany(SavedJob::class, 'seeker_id', 'seeker_id');
    }

    /**
     * Get the companies followed by the job seeker.
     */
    public function followedCompanies(): HasMany
    {
        return $this->hasMany(FollowedCompany::class, 'seeker_id', 'seeker_id');
    }

    /**
     * Get the work experiences for the job seeker.
     */
    public function experiences(): HasMany
    {
        return $this->hasMany(JobSeekerExperience::class, 'seeker_id', 'seeker_id');
    }

    /**
     * Get the educations for the job seeker.
     */
    public function educations(): HasMany
    {
        return $this->hasMany(JobSeekerEducation::class, 'seeker_id', 'seeker_id');
    }

    /**
     * Get the skills for the job seeker.
     */
    public function skills(): HasMany
    {
        return $this->hasMany(JobSeekerSkill::class, 'seeker_id', 'seeker_id');
    }

    /**
     * Get the languages for the job seeker.
     */
    public function languages(): HasMany
    {
        return $this->hasMany(JobSeekerLanguage::class, 'seeker_id', 'seeker_id');
    }

    /**
     * Get the certifications for the job seeker.
     */
    public function certifications(): HasMany
    {
        return $this->hasMany(JobSeekerCertification::class, 'seeker_id', 'seeker_id');
    }

    /**
     * Get the references for the job seeker.
     */
    public function references(): HasMany
    {
        return $this->hasMany(JobSeekerReference::class, 'seeker_id', 'seeker_id');
    }

    /**
     * Get the category preferences for the job seeker.
     */
    public function categoryPreferences(): HasMany
    {
        return $this->hasMany(JobSeekerCategoryPreference::class, 'seeker_id', 'seeker_id');
    }

    /**
     * Get the full name of the job seeker.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
