<?php

namespace App\Http\Controllers;

use App\Models\Url;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Admin dashboard with statistics.
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_urls' => Url::count(),
            'total_clicks' => Url::sum('clicks'),
            'active_urls' => Url::where('is_active', true)->count(),
        ];

        $recentUrls = Url::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $topUrls = Url::with('user')
            ->orderBy('clicks', 'desc')
            ->limit(10)
            ->get();

        $urlsByDay = Url::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentUrls', 'topUrls', 'urlsByDay'));
    }

    /**
     * List all URLs with filtering.
     */
    public function urls(Request $request)
    {
        $query = Url::with('user');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('short_code', 'like', "%{$search}%")
                    ->orWhere('original_url', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $urls = $query->paginate(20)->withQueryString();

        return view('admin.urls', compact('urls'));
    }

    /**
     * List all users.
     */
    public function users(Request $request)
    {
        $query = User::withCount('urls');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users', compact('users'));
    }

    /**
     * Toggle URL active status.
     */
    public function toggleUrlStatus(Url $url)
    {
        $url->update(['is_active' => !$url->is_active]);
        
        // Clear cache
        Cache::forget("url:{$url->short_code}");

        return back()->with('success', 'อัปเดตสถานะ URL สำเร็จ!');
    }

    /**
     * Delete a URL.
     */
    public function deleteUrl(Url $url)
    {
        Cache::forget("url:{$url->short_code}");
        $url->delete();

        return back()->with('success', 'ลบ URL สำเร็จ!');
    }

    /**
     * Toggle user admin status.
     */
    public function toggleAdminStatus(User $user)
    {
        // Prevent removing admin from yourself
        if ($user->id === Auth::id()) {
            return back()->with('error', 'ไม่สามารถเปลี่ยนสถานะ Admin ของตัวเองได้');
        }

        $user->update(['is_admin' => !$user->is_admin]);

        return back()->with('success', 'อัปเดตสถานะผู้ใช้สำเร็จ!');
    }

    /**
     * Delete a user and all their URLs.
     */
    public function deleteUser(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'ไม่สามารถลบบัญชีของตัวเองได้');
        }

        // Clear cache for all user's URLs
        foreach ($user->urls as $url) {
            Cache::forget("url:{$url->short_code}");
        }

        $user->delete();

        return back()->with('success', 'ลบผู้ใช้สำเร็จ!');
    }
}
