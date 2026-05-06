<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Brand;
use App\Models\SerialNumber;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'brand', 'supplier'])->paginate(15);
        return view('pages.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $suppliers  = Supplier::all();
        $brands     = Brand::orderBy('name')->get();
        return view('pages.products.create', compact('categories', 'suppliers', 'brands'));
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        // Checkbox handling
        $data['serial_tracking'] = $request->boolean('serial_tracking');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 's3');
        }

        Product::create($data);
        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $suppliers  = Supplier::all();
        $brands     = Brand::orderBy('name')->get();
        return view('pages.products.edit', compact('product', 'categories', 'suppliers', 'brands'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $data['serial_tracking'] = $request->boolean('serial_tracking');

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('s3')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 's3');
        }

        $product->update($data);
        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->orderItems()->count() > 0) {
            return redirect()->route('admin.products.index')->with('error', 'Cannot delete product that has been ordered.');
        }

        // Delete available serial numbers for this product
        $product->serialNumbers()->where('status', 'available')->delete();

        if ($product->image) {
            Storage::disk('s3')->delete($product->image);
        }

        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
