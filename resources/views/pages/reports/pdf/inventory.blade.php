<!DOCTYPE html>
<html>
<head>
    <title>Inventory Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #1F3A6E; }
        .header p { margin: 5px 0; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #1a2942; color: #fff; }
        .low-stock { color: red; font-weight: bold; }
        .footer { text-align: right; margin-top: 30px; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h2>SmartPOS Inventory Report</h2>
        <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Product Name</th>
                <th>Price (KES)</th>
                <th>Stock Level</th>
                <th>Reorder Level</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->sku }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ number_format($product->price, 2) }}</td>
                <td>{{ $product->stock_quantity }}</td>
                <td>{{ $product->reorder_level }}</td>
                @if($product->stock_quantity <= $product->reorder_level)
                    <td class="low-stock">LOW STOCK</td>
                @else
                    <td>Adequate</td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Page 1 of 1
    </div>
</body>
</html>
