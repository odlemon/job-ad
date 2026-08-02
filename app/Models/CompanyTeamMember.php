<?php

namespace App\Models;

use App\Support\TeamPermissions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyTeamMember extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'invited_by',
        'name',
        'email',
        'role',
        'status',
        'invite_token',
        'invited_at',
        'joined_at',
        'last_active_at',
        'jobs_posted',
    ];

    protected $casts = [
        'invited_at' => 'datetime',
        'joined_at' => 'datetime',
        'last_active_at' => 'datetime',
        'jobs_posted' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function can(string $capability): bool
    {
        return TeamPermissions::can($this->role, $capability);
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->name)) ?: [];
        $initials = '';
        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= strtoupper(substr($part, 0, 1));
        }

        return $initials ?: strtoupper(substr($this->email, 0, 2));
    }

    public function lastActiveLabel(): string
    {
        if (!$this->last_active_at) {
            return $this->isPending() ? 'Invite pending' : 'Never';
        }

        return $this->last_active_at->diffForHumans();
    }
}
