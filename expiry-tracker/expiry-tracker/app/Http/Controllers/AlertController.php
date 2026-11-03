<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category', 'user'])
            ->visibleTo($request->user())
            ->alerts()
            ->where('disposal_status', 'active')
            ->when($request->type === 'expired', fn ($q) => $q->whereDate('expiry_date', '<', now()->toDateString()))
            ->when($request->type === 'expiring_soon', fn ($q) => $q->whereBetween('expiry_date', [now()->toDateString(), now()->addDays(30)->toDateString()]))
            ->when($request->state === 'dismissed', fn ($q) => $q->whereNotNull('alert_dismissed_at'), fn ($q) => $q->whereNull('alert_dismissed_at'))
            ->orderBy('expiry_date')
            ->paginate(15)
            ->withQueryString();

        return view('alerts.index', compact('products'));
    }

    public function toggle(Request $request, Product $product)
    {
        abort_unless($request->user()->isAdmin(), 403);
        abort_unless($product->expiry_status !== 'safe', 422);
        $product->update(['alert_dismissed_at' => $product->alert_dismissed_at ? null : now()]);

        return back()->with('success', $product->alert_dismissed_at ? 'Alert dismissed.' : 'Alert restored.');
    }
}
