<?php

namespace App\Http\Controllers;

use App\Models\Url;
use App\Models\UrlClick;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UrlController extends Controller
{
    /**
     * Display user's dashboard with their URLs.
     */
    public function dashboard()
    {
        /** @var User $user */
        $user = Auth::user();
        
        $urls = $user->urls()
            ->withCount('urlClicks')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $totalClicks = $user->urls()->sum('clicks');
        $totalUrls = $user->urls()->count();

        return view('dashboard', compact('urls', 'totalClicks', 'totalUrls'));
    }

    /**
     * Show form to create a new short URL.
     */
    public function create()
    {
        return view('urls.create');
    }

    /**
     * Store a new short URL.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'original_url' => ['required', 'url', 'max:2048'],
            'title' => ['nullable', 'string', 'max:255'],
            'custom_code' => ['nullable', 'string', 'alpha_dash', 'min:4', 'max:10', 'unique:urls,short_code'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);

        $shortCode = $validated['custom_code'] ?? Url::generateShortCode();

        /** @var User $user */
        $user = Auth::user();
        
        $url = $user->urls()->create([
            'short_code' => $shortCode,
            'original_url' => $validated['original_url'],
            'title' => $validated['title'],
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        return redirect()->route('urls.show', $url)
            ->with('success', 'สร้าง Short URL สำเร็จ!');
    }

    /**
     * Display a specific URL's details.
     */
    public function show(Url $url)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Ensure user can only view their own URLs (unless admin)
        if ($url->user_id !== Auth::id() && !$user->isAdmin()) {
            abort(403);
        }

        $clicksByDay = $url->urlClicks()
            ->select(DB::raw('DATE(clicked_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        $clicksByDevice = $url->urlClicks()
            ->select('device', DB::raw('COUNT(*) as count'))
            ->groupBy('device')
            ->get();

        $clicksByBrowser = $url->urlClicks()
            ->select('browser', DB::raw('COUNT(*) as count'))
            ->groupBy('browser')
            ->get();

        return view('urls.show', compact('url', 'clicksByDay', 'clicksByDevice', 'clicksByBrowser'));
    }

    /**
     * Show form to edit a URL.
     */
    public function edit(Url $url)
    {
        /** @var User $user */
        $user = Auth::user();
        
        if ($url->user_id !== Auth::id() && !$user->isAdmin()) {
            abort(403);
        }

        return view('urls.edit', compact('url'));
    }

    /**
     * Update a URL.
     */
    public function update(Request $request, Url $url)
    {
        /** @var User $user */
        $user = Auth::user();
        
        if ($url->user_id !== Auth::id() && !$user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $url->update($validated);

        // Clear cache for this URL
        Cache::forget("url:{$url->short_code}");

        return redirect()->route('urls.show', $url)
            ->with('success', 'อัปเดต URL สำเร็จ!');
    }

    /**
     * Delete a URL.
     */
    public function destroy(Url $url)
    {
        /** @var User $user */
        $user = Auth::user();
        
        if ($url->user_id !== Auth::id() && !$user->isAdmin()) {
            abort(403);
        }

        // Clear cache
        Cache::forget("url:{$url->short_code}");
        
        $url->delete();

        return redirect()->route('dashboard')
            ->with('success', 'ลบ URL สำเร็จ!');
    }

    /**
     * Redirect to original URL (public access).
     * Using caching for high performance.
     */
    public function redirect(string $shortCode, Request $request)
    {
        // Try to get from cache first for high performance
        $url = Cache::remember("url:{$shortCode}", 3600, function () use ($shortCode) {
            return Url::where('short_code', $shortCode)->first();
        });

        if (!$url) {
            abort(404, 'URL ไม่พบ');
        }

        if (!$url->isAccessible()) {
            abort(410, 'URL นี้ไม่สามารถเข้าถึงได้');
        }

        // Record click asynchronously (using queue in production)
        $url->incrementClicks();
        UrlClick::recordClick($url, $request);

        return redirect()->away($url->original_url);
    }
}
