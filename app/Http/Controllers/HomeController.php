<?php

namespace App\Http\Controllers;

use App\Models\Thesis;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Record visit only once per session
        if (!session()->has('has_visited')) {
            \App\Models\Visit::create([
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            session()->put('has_visited', true);
        }
        
        // Calculate stats
        $totalTheses = Thesis::where('status', 'approved')->count();
        $totalUsers = User::count();
        
        // Monthly Views (Starts from 0 each month)
        $monthlyViews = \App\Models\Visit::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Optional: Add initial offset if needed (e.g. 1250)
        $displayViews = $monthlyViews; 

        $stats = [
            'total_theses' => $totalTheses,
            'total_users' => $totalUsers,
            'total_views' => $displayViews,
        ];

        $latestTheses = Thesis::with('user.department')
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        return view('welcome', compact('stats', 'latestTheses'));
    }

    public function verify($hash)
    {
        $thesis = Thesis::with('user.department.faculty')
            ->where('verification_hash', $hash)
            ->first();

        if (!$thesis) {
            return view('verify', ['success' => false]);
        }

        return view('verify', [
            'success' => true,
            'thesis' => $thesis
        ]);
    }
}
