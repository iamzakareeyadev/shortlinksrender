<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
        $user = Auth::user();
        
        if (!$user || !$user->isAdmin()) {
            abort(403, 'ไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        return $next($request);
    }
}
