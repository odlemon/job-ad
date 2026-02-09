<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employer extends Model
{
    protected $primaryKey = 'employer_id';

    protected $fillable = [
        'user_id',
        'company_id',
        'company_name',
        'company_logo',
        'company_description',
        'industry',
        'company_size',
        'website',
        'address',
        'coin_balance',
        'verified_at',
    ];

    protected $casts = [
        'coin_balance' => 'integer',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the user that owns the employer profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the company associated with this employer.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
