<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'transaction_id',
        'category',
        'payer_name',
        'description',
        'payment_method',
        'amount',
        'coins_amount',
        'currency',
        'coin_package_id',
        'status',
        'paid_at',
        'company_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public const CATEGORIES = [
        'job_ads',
        'tender_ads',
        'website_ads',
        'course_ads',
        'coins',
        'lpo',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
