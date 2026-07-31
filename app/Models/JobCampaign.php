<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobCampaign extends Model
{
    protected $fillable = [
        'job_advertisement_id',
        'campaign_type_id',
        'duration_days',
        'status',
        'payment_method',
        'launched_at',
        'ends_at',
        'views_count',
        'clicks_count',
        'shares_count',
        'saved_count',
        'messages_count',
        'invitation_sent_count',
    ];

    protected $casts = [
        'launched_at' => 'datetime',
        'ends_at' => 'datetime',
        'duration_days' => 'integer',
        'views_count' => 'integer',
        'clicks_count' => 'integer',
        'shares_count' => 'integer',
        'saved_count' => 'integer',
        'messages_count' => 'integer',
        'invitation_sent_count' => 'integer',
    ];

    public function jobAdvertisement(): BelongsTo
    {
        return $this->belongsTo(JobAdvertisement::class);
    }

    public function campaignType(): BelongsTo
    {
        return $this->belongsTo(CampaignType::class);
    }
}
