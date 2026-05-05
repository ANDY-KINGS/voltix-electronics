<!DOCTYPE html>
<html>
<head>
    <title>Payment Confirmation - Voltix Electronix</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #1F3A6E; color: white; padding: 15px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { margin-top: 20px; padding: 10px; }
        .footer { margin-top: 30px; font-size: 0.8em; color: #777; text-align: center; }
        .totals { margin-top: 20px; border-top: 1px solid #ddd; padding-top: 10px; }
        .warranty-note { font-size: 0.85em; color: #27AE60; margin-top: 15px; padding: 10px; background: #e9f7ef; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin: 0;">Payment Confirmation</h2>
            <p style="margin: 5px 0 0; font-size: 0.9em;">Voltix Electronix</p>
        </div>
        <div class="content">
            <p>Thank you for your purchase.</p>
            <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
            <p><strong>Amount Paid:</strong> KES {{ number_format($order->payment->amount ?? $order->total_amount, 2) }}</p>
            @if(isset($order->payment) && $order->payment->method === 'mpesa')
                <p><strong>M-Pesa Code:</strong> {{ $order->payment->mpesa_code ?? 'Pending' }}</p>
            @endif
            
            <div class="warranty-note">
                <strong><i class="fas fa-shield-alt"></i> Warranty Information:</strong><br>
                Please keep this receipt (or order number) and the original packaging. Most electronics come with a manufacturer warranty covering defects.
            </div>

            <div class="totals">
                <p>We hope to see you again soon!</p>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Voltix Electronix. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
