<?php

namespace Itsmurumba\Otp\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Otp extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'identifier',
        'code',
        'channel',
        'verified',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'verified' => 'boolean',
        'expires_at' => 'datetime',
    ];

    /**
     * Scope a query to only include valid OTPs.
     */
    public function scopeValid(Builder $query): void
    {
        $query->where('expires_at', '>', now())
            ->where('verified', false);
    }

    /**
     * Scope a query to only include OTPs for a specific identifier.
     */
    public function scopeForIdentifier(Builder $query, string $identifier): void
    {
        $query->where('identifier', $identifier);
    }

    /**
     * Mark the OTP as verified.
     */
    public function markAsVerified(): bool
    {
        return $this->update(['verified' => true]);
    }

    /**
     * Check if the OTP is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if the OTP is valid.
     */
    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->verified;
    }
}
