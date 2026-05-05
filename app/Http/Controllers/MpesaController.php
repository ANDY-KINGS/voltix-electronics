<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
// use App\Jobs\PaymentConfirmationEmail;
use App\Events\PaymentReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    public function callback(Request $request)
    {
        Log::info('M-Pesa Callback Received:', $request->all());
        
        $callbackBody = json_decode($request->getContent());
        $result = $callbackBody->Body->stkCallback;

        $checkoutRequestID = $result->CheckoutRequestID;
        $payment = Payment::where('mpesa_code', $checkoutRequestID)->first();

        if (!$payment) {
            Log::error('Payment record not found for CheckoutRequestID: ' . $checkoutRequestID);
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        }

        if ($result->ResultCode == 0) {
            // Success
            $meta = $result->CallbackMetadata->Item;
            $mpesaReceiptNumber = '';
            
            foreach ($meta as $item) {
                if ($item->Name == 'MpesaReceiptNumber') {
                    $mpesaReceiptNumber = $item->Value;
                }
            }

            $payment->update([
                'status' => 'success',
                'mpesa_code' => $mpesaReceiptNumber,
                'paid_at' => now(),
            ]);

            $order = $payment->order;
            $order->update(['status' => 'completed']);

            // Broadcast Event
            broadcast(new PaymentReceived($order->id, 'success', $mpesaReceiptNumber, $payment->amount));
            
            // Dispatch Email job
            // PaymentConfirmationEmail::dispatch($order);

        } else {
            // Failed
            $payment->update(['status' => 'failed']);
            $order = $payment->order;
            // broadcast(new PaymentReceived($order->id, 'failed', null, $payment->amount));
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }

    public function checkStatus($order_id)
    {
        $order = Order::findOrFail($order_id);
        $payment = $order->mpesaPayment;

        return response()->json([
            'status' => $payment ? $payment->status : 'pending'
        ]);
    }
}
