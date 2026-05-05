<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\WarrantyClaim;
use App\Models\SerialNumber;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $totalRevenueToday = Order::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->sum('total_amount');

        $totalOrdersToday = Order::whereDate('created_at', $today)->count();
        $lowStockCount    = Product::lowStock()->count();
        $totalProducts    = Product::count();

        // New electronics metrics
        $openWarrantyClaims = WarrantyClaim::where('status', 'open')->count();
        $serialsSold        = SerialNumber::sold()->count();
        $availableSerials   = SerialNumber::available()->count();

        // Last 7 days sales data
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date  = Carbon::today()->subDays($i);
            $sales = Order::whereDate('created_at', $date)
                ->where('status', 'completed')
                ->sum('total_amount');
            $last7Days->push([
                'date'  => $date->format('M d'),
                'sales' => $sales
            ]);
        }

        // Top 5 selling products this month
        $topProducts = \App\Models\OrderItem::selectRaw('product_id, SUM(quantity) as total_sold')
            ->whereHas('order', function ($query) {
                $query->whereMonth('created_at', Carbon::now()->month)
                      ->where('status', 'completed');
            })
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->with('product')
            ->get();

        $recentOrders  = Order::with('customer')->latest()->take(10)->get();
        $lowStockItems = Product::lowStock()->get();

        return view('pages.dashboard', compact(
            'totalRevenueToday', 'totalOrdersToday', 'lowStockCount',
            'totalProducts', 'last7Days', 'topProducts', 'recentOrders', 'lowStockItems',
            'openWarrantyClaims', 'serialsSold', 'availableSerials'
        ));
    }
}
