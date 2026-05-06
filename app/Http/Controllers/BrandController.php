<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Http\Requests\BrandRequest;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::withCount('products')->orderBy('name')->paginate(15);
        return view('pages.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('pages.brands.create');
    }

    public function store(BrandRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('brands', 's3');
        }

        Brand::create($data);
        return redirect()->route('admin.brands.index')->with('success', 'Brand created successfully.');
    }

    public function edit(Brand $brand)
    {
        return view('pages.brands.edit', compact('brand'));
    }

    public function update(BrandRequest $request, Brand $brand)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            if ($brand->logo) {
                Storage::disk('s3')->delete($brand->logo);
            }
            $data['logo'] = $request->file('logo')->store('brands', 's3');
        }

        $brand->update($data);
        return redirect()->route('admin.brands.index')->with('success', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->products()->count() > 0) {
            return redirect()->route('admin.brands.index')
                ->with('error', 'Cannot delete brand "' . $brand->name . '" — it has ' . $brand->products()->count() . ' product(s) associated.');
        }

        if ($brand->logo) {
            Storage::disk('s3')->delete($brand->logo);
        }

        $brand->delete();
        return redirect()->route('admin.brands.index')->with('success', 'Brand deleted successfully.');
    }
}
