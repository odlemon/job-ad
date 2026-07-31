<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'title',
        'badge',
        'badges',
        'level',
        'duration',
        'format',
        'price',
        'image_url',
        'provider',
        'instructor',
        'seats',
        'start_date',
        'phone',
        'email',
        'overview',
        'is_active',
    ];

    protected $casts = [
        'badges' => 'array',
        'start_date' => 'date',
        'is_active' => 'boolean',
    ];
}
