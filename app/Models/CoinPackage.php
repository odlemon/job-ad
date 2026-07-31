<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoinPackage extends Model
{
    protected $fillable = [
        'name',
        'coins_amount',
        'price',
        'currency',
        'description',
        'status',
        'sort_order',
        'icon',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'coins_amount' => 'integer',
    ];

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
