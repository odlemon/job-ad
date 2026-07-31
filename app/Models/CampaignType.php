<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampaignType extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'coins_price',
        'scr_price',
        'duration_days',
        'est_reach_min',
        'est_reach_max',
        'features',
        'is_popular',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'is_popular' => 'boolean',
        'coins_price' => 'integer',
        'scr_price' => 'integer',
        'duration_days' => 'integer',
        'est_reach_min' => 'integer',
        'est_reach_max' => 'integer',
    ];

    public function jobCampaigns(): HasMany
    {
        return $this->hasMany(JobCampaign::class);
    }
}
