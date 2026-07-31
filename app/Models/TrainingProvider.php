<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingProvider extends Model
{
    protected $fillable = [
        'name',
        'subtitle',
        'courses_available',
        'tagline',
        'is_featured',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
    ];
}
