<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyReview extends Model
{
    protected $fillable = [
        'company_id',
        'seeker_id',
        'rating',
        'work_life_balance',
        'benefits_perks',
        'work_environment_culture',
        'career_growth_development',
        'management_leadership',
        'employee_support_wellbeing',
        'role',
        'location',
        'employment_status',
        'good_things',
        'challenges',
        'helpful_count',
    ];

    protected $casts = [
        'rating' => 'integer',
        'work_life_balance' => 'float',
        'benefits_perks' => 'float',
        'work_environment_culture' => 'float',
        'career_growth_development' => 'float',
        'management_leadership' => 'float',
        'employee_support_wellbeing' => 'float',
        'helpful_count' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function jobSeeker(): BelongsTo
    {
        return $this->belongsTo(JobSeeker::class, 'seeker_id', 'seeker_id');
    }
}
