<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\SerialNumber;
use App\Http\Requests\PosAddItemRequest;
use App\Http\Requests\PosCheckoutRequest;
use App\Services\MpesaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class PosController extends Controller
{
    public function index()
    {
        $categories = Category::with('products')->get();
        $products   = Product::where('is_active', true)->with('brand')->get();

        // Available serials grouped by product_id (for serial-tracked items)
        $serials = SerialNumber::available()->with('product')->get()->groupBy('product_id');

        return view('pages.pos.index', compact('categories', 'products', 'serials'));
    }

    public function cart()
    {
        return response()->json(Session::get('pos_cart', ['items' => [], 'total' => 0]));
    }

    public function addItem(PosAddItemRequest $request)
    {
        $product = Product::findOrFail($request->product_id);

        $cart = Session::get('pos_cart', ['items' => [], 'total' => 0]);
        $qty  = $request->quantity;

        if (isset($cart['items'][$product->id])) {
            $cart['items'][$product->id]['qty']     += $qty;
            $cart['items'][$product->id]['subtotal'] += ($product->price * $qty);
        } else {
            $cart['items'][$product->id] = [
                'id'           => $product->id,
                'name'         => $product->name,
                'price'        => $product->price,
                'qty'          => $qty,
                'subtotal'     => $product->price * $qty,
                'needs_serial' => (bool) $product->serial_tracking,
            ];
        }

        $cart['total'] += ($product->price * $qty);
        Session::put('pos_cart', $cart);

        return response()->json($cart);
    }

    public function removeItem(Request $request)
    {
        $productId = $request->product_id;
        $cart = Session::get('pos_cart', ['items' => [], 'total' => 0]);

        if (isset($cart['items'][$productId])) {
            $cart['total'] -= $cart['items'][$productId]['subtotal'];
            unset($cart['items'][$productId]);
            Session::put('pos_cart', $cart);
        }

        return response()->json($cart);
    }

    public function emptyCart()
    {
        Session::forget('pos_cart');
        return response()->json(['message' => 'Cart emptied']);
    }

    public function checkout(PosCheckoutRequest $request, MpesaService $mpesaService)
    {
        $cart = Session::get('pos_cart');
        if (!$cart || empty($cart['items'])) {
            return redirect()->back()->with('error', 'Cart is empty');
        }

        // Validate serial numbers for tracked items BEFORE creating anything
        foreach ($cart['items'] as $productId => $item) {
            if (!empty($item['needs_serial'])) {
                $serialId = $request->input("serial_numbers.$productId");
                if (!$serialId) {
                    return back()->withErrors(['serial' => 'Please assign a serial number to all tracked items.'])->withInput();
                }
                $serial = SerialNumber::where('id', $serialId)->where('status', 'available')->first();
                if (!$serial) {
                    return back()->withErrors(['serial' => 'Selected serial number for "' . $item['name'] . '" is no longer available.'])->withInput();
                }
            }
        }

        $discount    = $request->discount ?? 0;
        $totalAmount = $cart['total'] - $discount;
        $method      = $request->payment_method;

        try {
            DB::beginTransaction();

            // Create Order
            $order = Order::create([
                'user_id'      => auth()->id(),
                'customer_id'  => $request->customer_id,
                'order_number' => Order::generateOrderNumber(),
                'total_amount' => $totalAmount,
                'discount'     => $discount,
                'status'       => $method == 'cash' ? 'completed' : 'pending',
            ]);

            // Create OrderItems and Deduct Stock
            foreach ($cart['items'] as $productId => $item) {
                $product = Product::find($item['id']);

                $orderItem = OrderItem::create([
                    'order_id'        => $order->id,
                    'product_id'      => $item['id'],
                    'quantity'        => $item['qty'],
                    'unit_price'      => $item['price'],
                    'subtotal'        => $item['subtotal'],
                    'warranty_months' => $product->warranty_months ?? null,
                    'warranty_expiry' => $product->warranty_months
                        ? Carbon::now()->addMonths($product->warranty_months)->toDateString()
                        : null,
                ]);

                // Assign serial number if tracked
                if (!empty($item['needs_serial'])) {
                    $serialId = $request->input("serial_numbers.$productId");
                    $serial   = SerialNumber::find($serialId);
                    if ($serial) {
                        $serial->update(['status' => 'sold', 'order_item_id' => $orderItem->id]);
                        $orderItem->update(['serial_number_id' => $serial->id]);
                    }
                }

                Product::where('id', $item['id'])->decrement('stock_quantity', $item['qty']);
            }

            // Handle Payment
            if ($method == 'cash') {
                Payment::create([
                    'order_id' => $order->id,
                    'method'   => 'cash',
                    'amount'   => $totalAmount,
                    'status'   => 'success',
                    'paid_at'  => now(),
                ]);

                DB::commit();
                return redirect()->route('pos.receipt', $order->id);

            } elseif ($method == 'mpesa') {
                $response = $mpesaService->stkPush(
                    $request->phone_number,
                    $totalAmount,
                    $order->order_number,
                    'Payment for Order ' . $order->order_number
                );

                Payment::create([
                    'order_id'     => $order->id,
                    'method'       => 'mpesa',
                    'amount'       => $totalAmount,
                    'status'       => 'pending',
                    'phone_number' => $request->phone_number,
                    'mpesa_code'   => $response->CheckoutRequestID,
                ]);

                DB::commit();
                return view('pages.pos.waiting', compact('order', 'totalAmount', 'request'));
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Checkout failed: ' . $e->getMessage());
        }
    }

    public function receipt(Order $order)
    {
        $order->load(['items.product.brand', 'items.serialNumber', 'payments', 'user', 'customer']);
        return view('pages.pos.receipt', compact('order'));
    }

    /**
     * AJAX: Create order, optionally record cash portion, fire M-Pesa STK push.
     * Returns JSON { success, order_id, checkout_request_id } or { success:false, message }
     */
    public function initiateMpesa(Request $request, MpesaService $mpesaService)
    {
        $request->validate([
            'phone_number' => ['required', 'string', 'regex:/^254[0-9]{9}$/'],
            'mpesa_amount' => ['required', 'numeric', 'min:1'],
            'cash_amount'  => ['nullable', 'numeric', 'min:0'],
            'customer_id'  => ['nullable', 'exists:customers,id'],
            'discount'     => ['nullable', 'numeric', 'min:0'],
        ]);

        $cart = Session::get('pos_cart');
        if (!$cart || empty($cart['items'])) {
            return response()->json(['success' => false, 'message' => 'Cart is empty'], 422);
        }

        // Serial validation
        foreach ($cart['items'] as $productId => $item) {
            if (!empty($item['needs_serial'])) {
                $serialId = $request->input("serial_numbers.$productId");
                if (!$serialId) {
                    return response()->json(['success' => false, 'message' => 'Please assign serial numbers to all tracked items.'], 422);
                }
                $serial = SerialNumber::where('id', $serialId)->where('status', 'available')->first();
                if (!$serial) {
                    return response()->json(['success' => false, 'message' => 'A selected serial number is no longer available.'], 422);
                }
            }
        }

        $discount    = $request->discount ?? 0;
        $totalAmount = $cart['total'] - $discount;
        $mpesaAmount = (float) $request->mpesa_amount;
        $cashAmount  = (float) ($request->cash_amount ?? max(0, $totalAmount - $mpesaAmount));

        try {
            DB::beginTransaction();

            // Create Order
            $order = Order::create([
                'user_id'      => auth()->id(),
                'customer_id'  => $request->customer_id,
                'order_number' => Order::generateOrderNumber(),
                'total_amount' => $totalAmount,
                'discount'     => $discount,
                'status'       => 'pending',
            ]);

            // Create OrderItems, deduct stock, assign serials
            foreach ($cart['items'] as $productId => $item) {
                $product = Product::find($item['id']);

                $orderItem = OrderItem::create([
                    'order_id'        => $order->id,
                    'product_id'      => $item['id'],
                    'quantity'        => $item['qty'],
                    'unit_price'      => $item['price'],
                    'subtotal'        => $item['subtotal'],
                    'warranty_months' => $product->warranty_months ?? null,
                    'warranty_expiry' => $product->warranty_months
                        ? Carbon::now()->addMonths($product->warranty_months)->toDateString()
                        : null,
                ]);

                if (!empty($item['needs_serial'])) {
                    $serialId = $request->input("serial_numbers.$productId");
                    $serial   = SerialNumber::find($serialId);
                    if ($serial) {
                        $serial->update(['status' => 'sold', 'order_item_id' => $orderItem->id]);
                        $orderItem->update(['serial_number_id' => $serial->id]);
                    }
                }

                Product::where('id', $item['id'])->decrement('stock_quantity', $item['qty']);
            }

            // Cash portion (instant confirmation)
            if ($cashAmount > 0) {
                Payment::create([
                    'order_id' => $order->id,
                    'method'   => 'cash',
                    'amount'   => $cashAmount,
                    'status'   => 'success',
                    'paid_at'  => now(),
                ]);
            }

            // Fire M-Pesa STK Push
            $stkResponse = $mpesaService->stkPush(
                $request->phone_number,
                $mpesaAmount,
                $order->order_number,
                'Payment for Order ' . $order->order_number
            );

            // Pending M-Pesa payment
            Payment::create([
                'order_id'     => $order->id,
                'method'       => 'mpesa',
                'amount'       => $mpesaAmount,
                'status'       => 'pending',
                'phone_number' => $request->phone_number,
                'mpesa_code'   => $stkResponse->CheckoutRequestID,
            ]);

            DB::commit();
            Session::forget('pos_cart');

            return response()->json([
                'success'             => true,
                'order_id'            => $order->id,
                'checkout_request_id' => $stkResponse->CheckoutRequestID,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('M-Pesa initiate error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'STK Push failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Manually mark an M-Pesa payment as confirmed (cashier fallback when callback doesn't fire).
     */
    public function manualConfirmMpesa(Request $request)
    {
        $request->validate(['order_id' => ['required', 'exists:orders,id']]);

        $order = Order::findOrFail($request->order_id);

        // Only allow if order is still pending
        if ($order->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Order is not in a pending state.'], 422);
        }

        $payment = $order->mpesaPayment;
        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'No M-Pesa payment record found for this order.'], 422);
        }

        $payment->update([
            'status'  => 'success',
            'paid_at' => now(),
        ]);

        $order->update(['status' => 'completed']);

        // Clear cart
        Session::forget('pos_cart');

        return response()->json(['success' => true, 'order_id' => $order->id]);
    }
}
