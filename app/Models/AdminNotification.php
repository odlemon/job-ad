<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminNotification extends Model
{
    protected $fillable = [
        'reference_id',
        'title',
        'message',
        'method',
        'audience',
        'category',
        'status',
        'scheduled_at',
        'sent_at',
        'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function nextReferenceId(): string
    {
        $year = date('Y');
        $last = static::where('reference_id', 'like', "NOT-{$year}-%")
            ->orderByDesc('id')
            ->value('reference_id');
        if (!$last) {
            return "NOT-{$year}-001";
        }
        $num = (int) substr($last, -3);
        return sprintf('NOT-%s-%03d', $year, $num + 1);
    }
}
