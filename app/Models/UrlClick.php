<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UrlClick extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'url_id',
        'ip_address',
        'user_agent',
        'referer',
        'country',
        'device',
        'browser',
        'clicked_at',
    ];

    protected $casts = [
        'clicked_at' => 'datetime',
    ];

    /**
     * Get the URL that this click belongs to.
     */
    public function url(): BelongsTo
    {
        return $this->belongsTo(Url::class);
    }

    /**
     * Record a click with request information.
     */
    public static function recordClick(Url $url, $request): self
    {
        $userAgent = $request->userAgent();
        
        return self::create([
            'url_id' => $url->id,
            'ip_address' => $request->ip(),
            'user_agent' => substr($userAgent, 0, 255),
            'referer' => substr($request->header('referer') ?? '', 0, 255),
            'device' => self::detectDevice($userAgent),
            'browser' => self::detectBrowser($userAgent),
            'clicked_at' => now(),
        ]);
    }

    /**
     * Simple device detection.
     */
    protected static function detectDevice(?string $userAgent): string
    {
        if (!$userAgent) return 'unknown';
        
        $userAgent = strtolower($userAgent);
        
        if (str_contains($userAgent, 'mobile') || str_contains($userAgent, 'android')) {
            return 'mobile';
        }
        if (str_contains($userAgent, 'tablet') || str_contains($userAgent, 'ipad')) {
            return 'tablet';
        }
        
        return 'desktop';
    }

    /**
     * Simple browser detection.
     */
    protected static function detectBrowser(?string $userAgent): string
    {
        if (!$userAgent) return 'unknown';
        
        $userAgent = strtolower($userAgent);
        
        if (str_contains($userAgent, 'chrome')) return 'Chrome';
        if (str_contains($userAgent, 'firefox')) return 'Firefox';
        if (str_contains($userAgent, 'safari')) return 'Safari';
        if (str_contains($userAgent, 'edge')) return 'Edge';
        if (str_contains($userAgent, 'opera')) return 'Opera';
        
        return 'other';
    }
}
