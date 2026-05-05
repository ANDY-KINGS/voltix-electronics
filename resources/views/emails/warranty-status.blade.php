<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #1F3A6E, #27AE60); padding: 30px; text-align: center; color: white; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0 0; opacity: 0.9; font-size: 14px; }
        .body { padding: 30px; }
        .status-badge { display: inline-block; padding: 8px 20px; border-radius: 20px; font-weight: bold; font-size: 14px; margin: 10px 0; }
        .status-open      { background: #fde8e8; color: #c0392b; }
        .status-in_review { background: #fef9e7; color: #d68910; }
        .status-resolved  { background: #e9f7ef; color: #1e8449; }
        .status-rejected  { background: #f2f3f4; color: #616a6b; }
        .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .info-table td { padding: 10px 12px; border-bottom: 1px solid #ecf0f1; font-size: 14px; }
        .info-table td:first-child { font-weight: bold; color: #555; width: 40%; background: #f8f9fa; }
        .notes-box { background: #f8f9fa; border-left: 4px solid #27AE60; padding: 15px; border-radius: 4px; margin: 20px 0; font-size: 14px; }
        .footer { background: #1a2942; color: #cbd5e1; text-align: center; padding: 20px; font-size: 12px; }
        .footer a { color: #27AE60; text-decoration: none; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🔧 Warranty Claim Update</h1>
        <p>Voltix Electronix — Powering Your Digital Life</p>
    </div>

    <div class="body">
        <p>Dear <strong>{{ $claim->customer->name ?? 'Valued Customer' }}</strong>,</p>
        <p>We have an update on your warranty claim. Here are the details:</p>

        <table class="info-table">
            <tr>
                <td>Product</td>
                <td>{{ $claim->orderItem->product->name ?? 'N/A' }}</td>
            </tr>
            @if($claim->orderItem->serialNumber)
            <tr>
                <td>Serial Number</td>
                <td>{{ $claim->orderItem->serialNumber->serial_number }}</td>
            </tr>
            @endif
            <tr>
                <td>Claim Status</td>
                <td>
                    <span class="status-badge status-{{ $claim->status }}">
                        {{ strtoupper(str_replace('_', ' ', $claim->status)) }}
                    </span>
                </td>
            </tr>
            @if($claim->resolved_at)
            <tr>
                <td>Resolved On</td>
                <td>{{ $claim->resolved_at->format('d M Y, H:i') }}</td>
            </tr>
            @endif
        </table>

        @if($claim->notes)
        <div class="notes-box">
            <strong>Resolution Notes:</strong><br>
            {{ $claim->notes }}
        </div>
        @endif

        <p style="font-size: 14px; color: #666;">
            If you have any questions about your warranty claim, please visit our shop or contact us directly.
        </p>

        <p style="font-size: 14px;">
            Thank you for choosing <strong>Voltix Electronix</strong>!
        </p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} Voltix Electronix. All rights reserved.</p>
        <p>Electronics &amp; Gadgets | Nairobi, Kenya</p>
    </div>
</div>
</body>
</html>
