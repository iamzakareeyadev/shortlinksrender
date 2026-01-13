<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Parse DATABASE_URL for Render
        if ($url = env('DATABASE_URL')) {
            $parsed = parse_url($url);
            config([
                'database.default' => 'pgsql',
                'database.connections.pgsql.host' => $parsed['host'] ?? null,
                'database.connections.pgsql.port' => $parsed['port'] ?? 5432,
                'database.connections.pgsql.database' => ltrim($parsed['path'] ?? '', '/'),
                'database.connections.pgsql.username' => $parsed['user'] ?? null,
                'database.connections.pgsql.password' => $parsed['pass'] ?? null,
            ]);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
