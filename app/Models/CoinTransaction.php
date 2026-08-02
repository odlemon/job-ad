<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoinTransaction extends Model
{
    public const TYPE_PURCHASE = 'purchase';
    public const TYPE_SPEND = 'spend';
    public const TYPE_REWARD = 'reward';

    protected $fillable = [
        'employer_id',
        'type',
        'amount',
        'description',
        'reference_type',
        'reference_id',
    ];

    protected $casts = [
        'amount' => 'integer',
    ];

    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class, 'employer_id', 'employer_id');
    }
}
