<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        if ($request->user()->isAdmin() && $request->routeIs('dashboard')) {
            return redirect()->route('admin.dashboard');
        }

        $base = Product::query()->visibleTo($request->user());
        $today = now()->toDateString();
        $soon = now()->addDays(30)->toDateString();

        $stats = [
            'products' => (clone $base)->count(),
            'safe' => (clone $base)->whereDate('expiry_date', '>', $soon)->where('disposal_status', 'active')->count(),
            'expiring' => (clone $base)->whereBetween('expiry_date', [$today, $soon])->where('disposal_status', 'active')->count(),
            'expired' => (clone $base)->whereDate('expiry_date', '<', $today)->where('disposal_status', 'active')->count(),
            'disposed' => (clone $base)->where('disposal_status', 'disposed')->count(),
        ];

        if ($request->user()->isAdmin()) {
            $stats['users'] = User::count();
            $stats['categories'] = Category::count();
        }

        return view('dashboard', [
            'stats' => $stats,
            'recentProducts' => (clone $base)->with(['category', 'user'])->latest()->limit(6)->get(),
            'expiringProducts' => (clone $base)->with('category')->whereBetween('expiry_date', [$today, $soon])->where('disposal_status', 'active')->orderBy('expiry_date')->limit(5)->get(),
            'expiredProducts' => (clone $base)->with('category')->whereDate('expiry_date', '<', $today)->where('disposal_status', 'active')->orderBy('expiry_date')->limit(5)->get(),
        ]);
    }
}
