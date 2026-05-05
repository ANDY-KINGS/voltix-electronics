<?php

namespace App\Http\Controllers;

use App\Models\SerialNumber;
use App\Models\Product;
use Illuminate\Http\Request;

class SerialNumberController extends Controller
{
    public function index(Request $request)
    {
        $query = SerialNumber::with('product.brand')->latest();

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $serialNumbers = $query->paginate(20)->withQueryString();
        $products = Product::serialTracked()->orderBy('name')->get();

        return view('pages.serial-numbers.index', compact('serialNumbers', 'products'));
    }

    public function byProduct(Product $product)
    {
        $serials = $product->serialNumbers()->available()->get();
        return response()->json($serials);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id'   => 'required|exists:products,id',
            'bulk_serials' => 'nullable|string',
        ]);

        $productId = $request->product_id;
        $created = 0;

        if ($request->filled('bulk_serials')) {
            $lines = preg_split('/\r\n|\r|\n/', trim($request->bulk_serials));
            foreach ($lines as $line) {
                $sn = trim($line);
                if ($sn === '') continue;

                // Skip duplicates silently
                if (SerialNumber::where('serial_number', $sn)->exists()) continue;

                SerialNumber::create([
                    'product_id'    => $productId,
                    'serial_number' => $sn,
                    'status'        => 'available',
                ]);
                $created++;
            }
        }

        return redirect()->route('admin.serial-numbers.index')
            ->with('success', "$created serial number(s) imported successfully.");
    }

    public function destroy($id)
    {
        $serial = SerialNumber::findOrFail($id);

        if ($serial->status !== 'available') {
            return redirect()->back()->with('error', 'Cannot delete a sold or returned serial number.');
        }

        $serial->delete();
        return redirect()->back()->with('success', 'Serial number deleted.');
    }
}
