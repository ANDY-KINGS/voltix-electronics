<!DOCTYPE html>
<html>
<head>
    <title>Low Stock Alert</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #E74C3C; color: white; padding: 10px; text-align: center; }
        .content { margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f4f4f4; }
        .footer { margin-top: 30px; font-size: 0.8em; color: #777; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Low Stock Alert</h2>
        </div>
        <div class="content">
            <p>The following products are running low on stock and need to be reordered.</p>
            
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>SKU</th>
                        <th>Current Stock</th>
                        <th>Reorder Level</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->sku }}</td>
                            <td style="color: red; font-weight: bold;">{{ $product->stock_quantity }}</td>
                            <td>{{ $product->reorder_level }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} SmartPOS. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
