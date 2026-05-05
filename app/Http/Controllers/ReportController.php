<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\WarrantyClaim;
use App\Models\SerialNumber;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        return view('pages.reports.index');
    }

    public function sales(Request $request)
    {
        $query = Order::where('status', 'completed');

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                $request->date_from . ' 00:00:00',
                $request->date_to . ' 23:59:59'
            ]);
        }

        $sales = $query->latest()->get();
        return view('pages.reports.sales', compact('sales'));
    }

    public function inventory()
    {
        $products = Product::with(['category', 'supplier', 'brand'])->orderBy('stock_quantity', 'asc')->get();
        return view('pages.reports.inventory', compact('products'));
    }

    public function revenue(Request $request)
    {
        $query = OrderItem::with(['order', 'product'])->whereHas('order', function($q) {
            $q->where('status', 'completed');
        });

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                $request->date_from . ' 00:00:00',
                $request->date_to . ' 23:59:59'
            ]);
        }

        $items = $query->get();

        $totalRevenue = $items->sum('subtotal');
        $totalCost    = $items->sum(function($item) {
            return $item->quantity * ($item->product->cost_price ?? 0);
        });
        $profit = $totalRevenue - $totalCost;

        return view('pages.reports.revenue', compact('items', 'totalRevenue', 'totalCost', 'profit'));
    }

    public function warrantyReport()
    {
        // Claims by status
        $claimsByStatus = [
            'open'      => WarrantyClaim::where('status', 'open')->count(),
            'in_review' => WarrantyClaim::where('status', 'in_review')->count(),
            'resolved'  => WarrantyClaim::where('status', 'resolved')->count(),
            'rejected'  => WarrantyClaim::where('status', 'rejected')->count(),
            'total'     => WarrantyClaim::count(),
        ];

        // Warranties expiring in next 30 days
        $expiringSoon = OrderItem::with(['product.brand', 'serialNumber', 'order.customer'])
            ->whereNotNull('warranty_expiry')
            ->whereBetween('warranty_expiry', [Carbon::today(), Carbon::today()->addDays(30)])
            ->orderBy('warranty_expiry')
            ->get();

        // Top 5 most claimed products
        $topClaimed = WarrantyClaim::selectRaw('order_items.product_id, COUNT(warranty_claims.id) as claim_count,
                SUM(CASE WHEN warranty_claims.status = "resolved" THEN 1 ELSE 0 END) as resolved_count,
                SUM(CASE WHEN warranty_claims.status = "open" THEN 1 ELSE 0 END) as open_count')
            ->join('order_items', 'warranty_claims.order_item_id', '=', 'order_items.id')
            ->groupBy('order_items.product_id')
            ->orderByDesc('claim_count')
            ->take(5)
            ->with('orderItem.product.brand')
            ->get();

        return view('reports.warranty', compact('claimsByStatus', 'expiringSoon', 'topClaimed'));
    }

    public function warrantyExport($format)
    {
        $claims = WarrantyClaim::with(['orderItem.product.brand', 'orderItem.serialNumber', 'customer'])->latest()->get();

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf.warranty', compact('claims'));
            return $pdf->download('warranty-claims-report.pdf');
        }

        return redirect()->back()->with('error', 'Export format not supported yet.');
    }

    public function export($type, Request $request)
    {
        $exportFormat = $request->query('format', 'pdf');

        if ($exportFormat === 'pdf') {
            if ($type === 'inventory') {
                $products = Product::with(['category', 'brand', 'supplier'])->get();
                $pdf = Pdf::loadView('pages.reports.pdf.inventory', compact('products'));
                return $pdf->download('inventory-report.pdf');
            }
        }

        return redirect()->back()->with('error', 'Export format not fully implemented yet.');
    }
}
