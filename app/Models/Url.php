<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Url extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'short_code',
        'original_url',
        'title',
        'clicks',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'clicks' => 'integer',
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user that owns the URL.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the click records for this URL.
     */
    public function urlClicks(): HasMany
    {
        return $this->hasMany(UrlClick::class);
    }

    /**
     * Check if the URL is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if the URL is accessible (active and not expired).
     */
    public function isAccessible(): bool
    {
        return $this->is_active && !$this->isExpired();
    }

    /**
     * Get the short URL.
     */
    public function getShortUrlAttribute(): string
    {
        return url($this->short_code);
    }

    /**
     * Generate a unique short code.
     */
    public static function generateShortCode(int $length = 6): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $maxIndex = strlen($characters) - 1;
        
        do {
            $shortCode = '';
            for ($i = 0; $i < $length; $i++) {
                $shortCode .= $characters[random_int(0, $maxIndex)];
            }
        } while (self::where('short_code', $shortCode)->exists());

        return $shortCode;
    }

    /**
     * Increment the click count efficiently.
     */
    public function incrementClicks(): void
    {
        $this->increment('clicks');
    }

    /**
     * Scope for active URLs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for accessible URLs (active and not expired).
     */
    public function scopeAccessible($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }
}
