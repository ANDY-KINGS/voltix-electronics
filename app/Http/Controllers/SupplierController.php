<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::paginate(15);
        return view('pages.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('pages.suppliers.create');
    }

    public function store(StoreSupplierRequest $request)
    {
        Supplier::create($request->validated());
        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function edit(Supplier $supplier)
    {
        return view('pages.suppliers.edit', compact('supplier'));
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->validated());
        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->products()->count() > 0) {
            return redirect()->route('admin.suppliers.index')->with('error', 'Cannot delete supplier with associated products.');
        }
        $supplier->delete();
        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier deleted successfully.');
    }
}
