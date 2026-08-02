<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'website',
        'facebook',
        'email',
        'phone',
        'logo',
        'cover_image',
        'gallery_images',
        'industry',
        'size',
        'location',
        'founded_year',
        'registration_number',
        'linkedin',
        'twitter',
        'instagram',
        'culture_benefits',
        'benefits',
        'company_values',
        'faqs',
        'working_hours',
        'workplace_description',
        'is_active',
        'verified_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'founded_year' => 'integer',
        'gallery_images' => 'array',
        'benefits' => 'array',
        'company_values' => 'array',
        'faqs' => 'array',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the job advertisements for the company.
     */
    public function jobAdvertisements(): HasMany
    {
        return $this->hasMany(JobAdvertisement::class);
    }

    /**
     * Get the employer that owns this company.
     */
    public function employer(): HasOne
    {
        return $this->hasOne(Employer::class);
    }

    /**
     * Get the job seekers who follow this company.
     */
    public function followers(): HasMany
    {
        return $this->hasMany(FollowedCompany::class);
    }

    /**
     * Get the company reviews.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(CompanyReview::class);
    }
}
