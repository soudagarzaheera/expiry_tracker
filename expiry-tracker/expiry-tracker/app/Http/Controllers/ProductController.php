<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'user'])->visibleTo($request->user());
        $this->applyFilters($query, $request);

        return view('products.index', [
            'products' => $query->latest()->paginate(12)->withQueryString(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function create(Request $request)
    {
        return view('products.create', [
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
            'users' => $request->user()->isAdmin() ? User::where('is_active', true)->orderBy('name')->get() : collect(),
        ]);
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->isAdmin() && $request->filled('user_id')
            ? $request->integer('user_id') : $request->user()->id;
        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Product added successfully.');
    }

    public function show(Request $request, Product $product)
    {
        $this->authorize('view', $product);

        return view('products.show', compact('product'));
    }

    public function edit(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        return view('products.edit', [
            'product' => $product,
            'categories' => Category::where('is_active', true)->orWhere('id', $product->category_id)->orderBy('name')->get(),
            'users' => $request->user()->isAdmin() ? User::where('is_active', true)->orWhere('id', $product->user_id)->orderBy('name')->get() : collect(),
        ]);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $this->authorize('update', $product);
        $data = $request->validated();
        if (! $request->user()->isAdmin()) {
            unset($data['user_id']);
        }
        $product->update($data);

        return redirect()->route('products.show', $product)->with('success', 'Product updated successfully.');
    }

    public function destroy(Request $request, Product $product)
    {
        $this->authorize('delete', $product);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    public function dispose(Request $request, Product $product)
    {
        $this->authorize('dispose', $product);
        $product->update(['disposal_status' => 'disposed', 'disposed_at' => now()]);

        return back()->with('success', 'Product marked for disposal.');
    }

    public function renew(Request $request, Product $product)
    {
        $this->authorize('update', $product);
        $validated = $request->validate([
            'manufacturing_date' => ['required', 'date', 'before_or_equal:today'],
            'expiry_date' => ['required', 'date', 'after_or_equal:manufacturing_date'],
        ]);
        $product->update($validated + ['disposal_status' => 'active', 'disposed_at' => null, 'alert_dismissed_at' => null]);

        return back()->with('success', 'Expiry information renewed successfully.');
    }

    private function applyFilters($query, Request $request): void
    {
        $query->when($request->search, fn ($q, $search) => $q->where(fn ($inner) => $inner
            ->where('name', 'like', "%{$search}%")->orWhere('batch_number', 'like', "%{$search}%")))
            ->when($request->category_id, fn ($q, $category) => $q->where('category_id', $category))
            ->when($request->status, function ($q, $status) {
                $today = now()->toDateString(); $soon = now()->addDays(30)->toDateString();
                match ($status) {
                    'safe' => $q->whereDate('expiry_date', '>', $soon)->where('disposal_status', 'active'),
                    'expiring_soon' => $q->whereBetween('expiry_date', [$today, $soon])->where('disposal_status', 'active'),
                    'expired' => $q->whereDate('expiry_date', '<', $today)->where('disposal_status', 'active'),
                    'disposed' => $q->where('disposal_status', 'disposed'),
                    default => null,
                };
            });
    }
}
