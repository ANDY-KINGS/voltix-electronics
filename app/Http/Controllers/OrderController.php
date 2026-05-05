<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'user', 'payment'])->latest();

        if ($request->filled('order_number')) {
            $query->where('order_number', 'like', '%' . $request->order_number . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                $request->date_from . ' 00:00:00',
                $request->date_to . ' 23:59:59'
            ]);
        }

        $orders = $query->paginate(15);
        return view('pages.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load([
            'items.product.brand',
            'items.serialNumber',
            'items.warrantyClaim',
            'customer',
            'payment',
            'user'
        ]);
        return view('pages.orders.show', compact('order'));
    }
}
