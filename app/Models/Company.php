<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'website',
        'email',
        'phone',
        'logo',
        'cover_image',
        'gallery_images',
        'industry',
        'size',
        'location',
        'founded_year',
        'linkedin',
        'twitter',
        'culture_benefits',
        'is_active',
        'verified_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'founded_year' => 'integer',
        'gallery_images' => 'array',
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
     * Get the job seekers who follow this company.
     */
    public function followers(): HasMany
    {
        return $this->hasMany(FollowedCompany::class);
    }
}
