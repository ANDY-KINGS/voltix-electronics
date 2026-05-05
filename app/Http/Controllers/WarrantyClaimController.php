<?php

namespace App\Http\Controllers;

use App\Models\WarrantyClaim;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Http\Requests\WarrantyClaimRequest;
use App\Mail\WarrantyStatusEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class WarrantyClaimController extends Controller
{
    public function index(Request $request)
    {
        $query = WarrantyClaim::with(['orderItem.product.brand', 'orderItem.serialNumber', 'customer'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $claims = $query->paginate(15)->withQueryString();

        $counts = [
            'all'       => WarrantyClaim::count(),
            'open'      => WarrantyClaim::where('status', 'open')->count(),
            'in_review' => WarrantyClaim::where('status', 'in_review')->count(),
            'resolved'  => WarrantyClaim::where('status', 'resolved')->count(),
            'rejected'  => WarrantyClaim::where('status', 'rejected')->count(),
        ];

        return view('pages.warranty-claims.index', compact('claims', 'counts'));
    }

    public function create(Request $request)
    {
        $customers   = Customer::orderBy('name')->get();
        $orderItemId = $request->query('order_item_id');
        $selectedItem = $orderItemId ? OrderItem::with(['product', 'serialNumber'])->find($orderItemId) : null;

        return view('pages.warranty-claims.create', compact('customers', 'selectedItem'));
    }

    public function store(WarrantyClaimRequest $request)
    {
        WarrantyClaim::create($request->validated());
        return redirect()->route('warranty-claims.index')->with('success', 'Warranty claim raised successfully.');
    }

    public function show(WarrantyClaim $warrantyClaim)
    {
        $warrantyClaim->load(['orderItem.product.brand', 'orderItem.serialNumber', 'orderItem.order.customer', 'customer']);
        return view('pages.warranty-claims.show', compact('warrantyClaim'));
    }

    public function update(Request $request, WarrantyClaim $warrantyClaim)
    {
        $request->validate([
            'status' => 'required|in:open,in_review,resolved,rejected',
            'notes'  => 'nullable|string',
        ]);

        $data = [
            'status' => $request->status,
            'notes'  => $request->notes,
        ];

        if (in_array($request->status, ['resolved', 'rejected'])) {
            $data['resolved_at'] = now();
        }

        $warrantyClaim->update($data);
        $warrantyClaim->load(['orderItem.product', 'customer']);

        // Send email notification
        if ($warrantyClaim->customer && $warrantyClaim->customer->email) {
            try {
                Mail::to($warrantyClaim->customer->email)->send(new WarrantyStatusEmail($warrantyClaim));
            } catch (\Exception $e) {
                // Log silently — don't block the update
            }
        }

        return redirect()->route('warranty-claims.show', $warrantyClaim)
            ->with('success', 'Warranty claim updated successfully.');
    }

    public function updateStatus(Request $request, WarrantyClaim $warrantyClaim)
    {
        return $this->update($request, $warrantyClaim);
    }
}
