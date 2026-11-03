<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->query($request);

        return view('reports.index', [
            'products' => $query->paginate(25)->withQueryString(),
            'categories' => Category::orderBy('name')->get(),
            'summary' => $this->summary($request),
        ]);
    }

    public function print(Request $request)
    {
        return view('reports.print', [
            'products' => $this->query($request)->get(),
            'summary' => $this->summary($request),
        ]);
    }

    public function csv(Request $request)
    {
        $products = $this->query($request)->get();
        $filename = 'expiry-report-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($products) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Product', 'Category', 'Owner', 'Batch', 'Quantity', 'Unit', 'Manufactured', 'Expires', 'Status', 'Disposal']);
            foreach ($products as $product) {
                fputcsv($output, [
                    $product->name, $product->category->name, $product->user->name, $product->batch_number,
                    $product->quantity, $product->unit, $product->manufacturing_date->format('Y-m-d'),
                    $product->expiry_date->format('Y-m-d'), $product->status_label, ucfirst($product->disposal_status),
                ]);
            }
            fclose($output);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function query(Request $request): Builder
    {
        $query = Product::with(['category', 'user'])->visibleTo($request->user());
        $today = now()->toDateString();
        $soon = now()->addDays(30)->toDateString();

        $query->when($request->category_id, fn ($q, $id) => $q->where('category_id', $id))
            ->when($request->date_from, fn ($q, $date) => $q->whereDate('expiry_date', '>=', $date))
            ->when($request->date_to, fn ($q, $date) => $q->whereDate('expiry_date', '<=', $date));

        match ($request->get('type', 'inventory')) {
            'safe' => $query->whereDate('expiry_date', '>', $soon)->where('disposal_status', 'active'),
            'expiring_soon' => $query->whereBetween('expiry_date', [$today, $soon])->where('disposal_status', 'active'),
            'expired' => $query->whereDate('expiry_date', '<', $today)->where('disposal_status', 'active'),
            'disposed' => $query->where('disposal_status', 'disposed'),
            default => null,
        };

        return $query->orderBy('expiry_date');
    }

    private function summary(Request $request): array
    {
        $base = Product::query()->visibleTo($request->user());
        $today = now()->toDateString(); $soon = now()->addDays(30)->toDateString();

        return [
            'inventory' => (clone $base)->count(),
            'safe' => (clone $base)->whereDate('expiry_date', '>', $soon)->where('disposal_status', 'active')->count(),
            'expiring_soon' => (clone $base)->whereBetween('expiry_date', [$today, $soon])->where('disposal_status', 'active')->count(),
            'expired' => (clone $base)->whereDate('expiry_date', '<', $today)->where('disposal_status', 'active')->count(),
            'disposed' => (clone $base)->where('disposal_status', 'disposed')->count(),
        ];
    }
}
