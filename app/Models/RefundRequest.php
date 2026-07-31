<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefundRequest extends Model
{
    protected $fillable = [
        'request_id',
        'employer_id',
        'company_id',
        'amount',
        'currency',
        'coins_equivalent',
        'payment_method',
        'type',
        'status',
        'reason',
        'admin_notes',
        'processed_at',
        'processed_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public const TYPES = ['job', 'advertisement', 'coins', 'tender'];
    public const STATUSES = ['pending', 'processing', 'approved', 'completed', 'rejected'];

    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class, 'employer_id', 'employer_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public static function generateRequestId(): string
    {
        $year = date('Y');
        $last = static::whereRaw("YEAR(created_at) = ?", [$year])->orderByDesc('id')->first();
        $num = $last ? (int) preg_replace('/^REF-\d+-0*/', '', $last->request_id ?? '0') + 1 : 1;
        return sprintf('REF-%s-%03d', $year, $num);
    }
}
