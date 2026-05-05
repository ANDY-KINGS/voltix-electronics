@extends('layouts.app')

@section('title', 'POS Terminal')

@section('content')
<div class="row">
    <!-- Products Panel -->
    <div class="col-md-7 border-end">
        <ul class="nav nav-tabs mb-3" id="categoryTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#tab-all" type="button" role="tab">All</button>
            </li>
            @foreach($categories as $category)
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="cat-{{ $category->id }}-tab" data-bs-toggle="tab" data-bs-target="#tab-{{ $category->id }}" type="button" role="tab">{{ $category->name }}</button>
            </li>
            @endforeach
        </ul>

        <div class="mb-3">
            <input type="text" id="productSearch" class="form-control" placeholder="Search products...">
        </div>

        <div class="tab-content" id="categoryTabsContent" style="height: 600px; overflow-y: auto;">
            <div class="tab-pane fade show active" id="tab-all" role="tabpanel" tabindex="0">
                <div class="row row-cols-3 row-cols-md-4 g-3 product-grid">
                    @foreach($products as $product)
                        <div class="col product-card" data-name="{{ strtolower($product->name) }}" data-id="{{ $product->id }}">
                            <div class="card h-100 text-center cursor-pointer" onclick="addToCart({{ $product->id }})">
                                @if($product->image)
                                    <div style="height: 120px; background: #f8f9fa; display:flex; align-items:center; justify-content:center; overflow:hidden; border-bottom: 1px solid #eee;">
                                        <img src="{{ Storage::url($product->image) }}" style="max-height: 110px; max-width: 100%; object-fit: contain; padding: 6px;">
                                    </div>
                                @else
                                    <div style="height: 120px; background: #f0f0f0; display:flex; align-items:center; justify-content:center;">
                                        <i class="fas fa-box fa-2x text-muted"></i>
                                    </div>
                                @endif
                                <div class="card-body p-2">
                                    <h6 class="card-title mb-1" style="font-size:0.9rem;">{{ $product->name }}</h6>
                                    <p class="card-text text-primary font-weight-bold mb-1">KES {{ $product->price }}</p>
                                    <small class="text-muted stock-label">Stock: {{ $product->stock_quantity }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            @foreach($categories as $category)
            <div class="tab-pane fade" id="tab-{{ $category->id }}" role="tabpanel" tabindex="0">
                <div class="row row-cols-3 row-cols-md-4 g-3 product-grid">
                    @foreach($category->products->where('is_active', true) as $product)
                        <div class="col product-card" data-name="{{ strtolower($product->name) }}" data-id="{{ $product->id }}">
                            <div class="card h-100 text-center cursor-pointer" onclick="addToCart({{ $product->id }})">
                                @if($product->image)
                                    <div style="height: 120px; background: #f8f9fa; display:flex; align-items:center; justify-content:center; overflow:hidden; border-bottom: 1px solid #eee;">
                                        <img src="{{ Storage::url($product->image) }}" style="max-height: 110px; max-width: 100%; object-fit: contain; padding: 6px;">
                                    </div>
                                @else
                                    <div style="height: 120px; background: #f0f0f0; display:flex; align-items:center; justify-content:center;">
                                        <i class="fas fa-box fa-2x text-muted"></i>
                                    </div>
                                @endif
                                <div class="card-body p-2">
                                    <h6 class="card-title mb-1" style="font-size:0.9rem;">{{ $product->name }}</h6>
                                    <p class="card-text text-primary font-weight-bold mb-1">KES {{ $product->price }}</p>
                                    <small class="text-muted">Stock: {{ $product->stock_quantity }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Cart Panel -->
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Current Order</h5>
                <button type="button" class="btn btn-sm btn-danger" onclick="emptyCart()"><i class="fas fa-trash"></i> Empty</button>
            </div>
            <div class="card-body p-0" style="height: 380px; overflow-y: auto;">
                @if($errors->has('serial'))
                    <div class="alert alert-danger m-2 py-2">{{ $errors->first('serial') }}</div>
                @endif
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Serial #</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="cartTableBody">
                        <!-- Cart items loaded via JS -->
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-light p-3">
                <form id="checkoutForm" method="POST" action="{{ route('pos.checkout') }}">
                    @csrf
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Subtotal:</strong>
                        <span id="cartSubtotal">KES 0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 align-items-center">
                        <strong>Discount:</strong>
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <span class="input-group-text">KES</span>
                            <input type="number" name="discount" id="cartDiscount" class="form-control text-end" value="0" min="0" oninput="updateGrandTotal()">
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3 text-primary">
                        <h4 class="mb-0">Grand Total:</h4>
                        <h4 class="mb-0 font-weight-bold" id="cartGrandTotal">KES 0.00</h4>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Customer (Optional)</label>
                        <select name="customer_id" class="form-select form-select-sm">
                            <option value="">Walk-in Customer</option>
                            @foreach(\App\Models\Customer::all() as $cus)
                                <option value="{{ $cus->id }}">{{ $cus->name }} - {{ $cus->phone }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-block">Payment Method</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="payment_method" id="payCash" value="cash" autocomplete="off" checked>
                            <label class="btn btn-outline-success" for="payCash"><i class="fas fa-money-bill-wave"></i> Cash</label>

                            <input type="radio" class="btn-check" name="payment_method" id="payMpesa" value="mpesa" autocomplete="off">
                            <label class="btn btn-outline-success" for="payMpesa" id="mpesaOpenBtn"><i class="fas fa-mobile-alt"></i> M-Pesa / Split</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 mt-2 font-weight-bold" id="checkoutBtn" disabled>
                        <i class="fas fa-check-circle"></i> Complete Checkout (Cash)
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- M-Pesa Payment Modal -->
<div class="modal fade" id="mpesaModal" tabindex="-1" aria-labelledby="mpesaModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">

      <!-- Input State -->
      <div id="mpesaInputState">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="mpesaModalLabel"><i class="fas fa-mobile-alt me-2"></i> M-Pesa / Split Payment</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" onclick="resetMpesaModal()"></button>
        </div>
        <div class="modal-body p-4">
          <div class="alert alert-info py-2 mb-3">
            <strong>Grand Total:</strong> <span id="mpesaGrandTotal" class="fs-5 fw-bold text-primary">KES 0.00</span>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">M-Pesa Amount <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text">KES</span>
              <input type="number" id="mpesaAmountInput" class="form-control" placeholder="0" min="1" step="1" oninput="updateCashSplit()">
            </div>
            <div class="form-text">Enter the amount customer will pay via M-Pesa.</div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Cash Amount (Balance)</label>
            <div class="input-group">
              <span class="input-group-text">KES</span>
              <input type="text" id="mpesaCashDisplay" class="form-control bg-light" readonly value="0.00">
            </div>
            <div class="form-text text-muted">Remaining amount to be paid in cash.</div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Customer Phone Number <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-phone"></i></span>
              <input type="text" id="mpesaPhoneInput" class="form-control" placeholder="254700000000" maxlength="12">
            </div>
            <div class="form-text">Format: 254XXXXXXXXX (12 digits)</div>
          </div>

          <div id="mpesaInputError" class="alert alert-danger py-2 d-none"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="resetMpesaModal()">Cancel</button>
          <button type="button" class="btn btn-success px-4" onclick="sendStkPush()">
            <i class="fas fa-paper-plane me-1"></i> Send STK Push
          </button>
        </div>
      </div>

      <!-- Waiting State -->
      <div id="mpesaWaitingState" class="d-none">
        <div class="modal-header bg-warning">
          <h5 class="modal-title"><i class="fas fa-clock me-2"></i> Waiting for Customer</h5>
        </div>
        <div class="modal-body text-center p-4">
          <div class="spinner-border text-success mb-3" style="width:3.5rem;height:3.5rem;"></div>
          <h5 class="mb-1">STK Push Sent!</h5>
          <p class="text-muted mb-0">Ask the customer to enter their M-Pesa PIN on their phone.</p>
          <p class="text-muted small mt-2">Auto-checking every 5 seconds…</p>
          <div id="mpesaManualConfirmBox" class="d-none mt-3">
            <div class="alert alert-warning py-2 small mb-2">
              <i class="fas fa-exclamation-triangle me-1"></i>
              Taking longer than expected. If the customer has paid, click <strong>"Confirm Payment"</strong> to complete the order.
            </div>
            <button type="button" class="btn btn-success btn-sm px-4" onclick="manualConfirmPayment()">
              <i class="fas fa-check me-1"></i> Confirm Payment Received
            </button>
          </div>
        </div>
        <div class="modal-footer justify-content-center gap-2">
          <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal" onclick="resetMpesaModal()"><i class="fas fa-times me-1"></i> Cancel</button>
          <button type="button" class="btn btn-outline-secondary btn-sm" onclick="pollStatus()"><i class="fas fa-sync me-1"></i> Check Now</button>
        </div>
      </div>

      <!-- Success State -->
      <div id="mpesaSuccessState" class="d-none">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title"><i class="fas fa-check-circle me-2"></i> Payment Confirmed!</h5>
        </div>
        <div class="modal-body text-center p-5">
          <div style="font-size:4rem;" class="text-success mb-3"><i class="fas fa-check-circle"></i></div>
          <h5>M-Pesa payment received!</h5>
          <p class="text-muted">Redirecting to receipt…</p>
        </div>
      </div>

      <!-- Failed State -->
      <div id="mpesaFailedState" class="d-none">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title"><i class="fas fa-times-circle me-2"></i> Payment Failed</h5>
        </div>
        <div class="modal-body text-center p-4">
          <div style="font-size:3.5rem;" class="text-danger mb-3"><i class="fas fa-times-circle"></i></div>
          <p id="mpesaFailedMsg" class="text-muted">The payment was cancelled or failed. Please try again.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="resetMpesaModal()">Close</button>
          <button type="button" class="btn btn-warning" onclick="showMpesaInputState()"><i class="fas fa-redo me-1"></i> Try Again</button>
        </div>
      </div>

    </div>
  </div>
</div>

<style>
    .cursor-pointer { cursor: pointer; }
    .product-card:hover .card { border-color: var(--accent-color); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
</style>
@endsection

@push('scripts')
<script>
    let cartTotal = 0;
    let mpesaPollInterval = null;
    let mpesaOrderId = null;
    const availableSerials = @json($serials->map(fn($group) => $group->map(fn($s) => ['id' => $s->id, 'sn' => $s->serial_number])->values()));

    // Search filter
    document.getElementById('productSearch').addEventListener('keyup', function() {
        let val = this.value.toLowerCase();
        document.querySelectorAll('.product-grid .product-card').forEach(card => {
            card.style.display = card.dataset.name.includes(val) ? '' : 'none';
        });
    });

    // Open M-Pesa modal when M-Pesa radio is clicked
    document.getElementById('payMpesa').addEventListener('change', function() {
        if (this.checked) {
            openMpesaModal();
        }
    });

    // Reset back to Cash if modal is closed without completing
    document.getElementById('mpesaModal').addEventListener('hidden.bs.modal', function() {
        if (!mpesaOrderId) {
            document.getElementById('payCash').checked = true;
        }
    });

    function openMpesaModal() {
        if (cartTotal <= 0) {
            alert('Please add items to cart first.');
            document.getElementById('payCash').checked = true;
            return;
        }
        let discount = parseFloat(document.getElementById('cartDiscount').value) || 0;
        let grandTotal = Math.max(0, cartTotal - discount);
        document.getElementById('mpesaGrandTotal').textContent = 'KES ' + grandTotal.toFixed(2);
        document.getElementById('mpesaAmountInput').value = grandTotal.toFixed(0);
        document.getElementById('mpesaAmountInput').max = grandTotal;
        document.getElementById('mpesaCashDisplay').value = '0.00';
        showMpesaInputState();
        new bootstrap.Modal(document.getElementById('mpesaModal')).show();
    }

    function updateCashSplit() {
        let discount = parseFloat(document.getElementById('cartDiscount').value) || 0;
        let grandTotal = Math.max(0, cartTotal - discount);
        let mpesa = parseFloat(document.getElementById('mpesaAmountInput').value) || 0;
        let cash = Math.max(0, grandTotal - mpesa);
        document.getElementById('mpesaCashDisplay').value = cash.toFixed(2);
    }

    function showMpesaInputState() {
        document.getElementById('mpesaInputState').classList.remove('d-none');
        document.getElementById('mpesaWaitingState').classList.add('d-none');
        document.getElementById('mpesaSuccessState').classList.add('d-none');
        document.getElementById('mpesaFailedState').classList.add('d-none');
        document.getElementById('mpesaInputError').classList.add('d-none');
    }

    function resetMpesaModal() {
        clearInterval(mpesaPollInterval);
        mpesaPollInterval = null;
        mpesaOrderId = null;
        document.getElementById('mpesaManualConfirmBox').classList.add('d-none');
        showMpesaInputState();
    }

    function manualConfirmPayment() {
        if (!mpesaOrderId) return;
        clearInterval(mpesaPollInterval);
        fetch('{{ route("pos.mpesaManualConfirm") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ order_id: mpesaOrderId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('mpesaWaitingState').classList.add('d-none');
                document.getElementById('mpesaSuccessState').classList.remove('d-none');
                setTimeout(() => {
                    window.location.href = '{{ url("/pos/receipt") }}/' + mpesaOrderId;
                }, 1200);
            } else {
                alert(data.message || 'Could not confirm payment.');
            }
        })
        .catch(err => alert('Error: ' + err.message));
    }

    function sendStkPush() {
        let phone = document.getElementById('mpesaPhoneInput').value.trim();
        let mpesaAmount = parseFloat(document.getElementById('mpesaAmountInput').value) || 0;
        let discount = parseFloat(document.getElementById('cartDiscount').value) || 0;
        let grandTotal = Math.max(0, cartTotal - discount);
        let cashAmount = Math.max(0, grandTotal - mpesaAmount);

        // Basic validation
        let errorEl = document.getElementById('mpesaInputError');
        errorEl.classList.add('d-none');

        if (!/^254[0-9]{9}$/.test(phone)) {
            errorEl.textContent = 'Phone number must be in format 254XXXXXXXXX (12 digits).';
            errorEl.classList.remove('d-none');
            return;
        }
        if (mpesaAmount < 1) {
            errorEl.textContent = 'M-Pesa amount must be at least KES 1.';
            errorEl.classList.remove('d-none');
            return;
        }
        if (mpesaAmount > grandTotal) {
            errorEl.textContent = 'M-Pesa amount cannot exceed the grand total.';
            errorEl.classList.remove('d-none');
            return;
        }

        // Collect serial numbers from cart form
        let serialData = {};
        document.querySelectorAll('[name^="serial_numbers["]').forEach(sel => {
            let match = sel.name.match(/serial_numbers\[(\d+)\]/);
            if (match) serialData[match[1]] = sel.value;
        });

        // Collect customer & discount from the checkout form
        let customerId = document.querySelector('[name="customer_id"]').value;

        // Switch to waiting state
        document.getElementById('mpesaInputState').classList.add('d-none');
        document.getElementById('mpesaWaitingState').classList.remove('d-none');

        let payload = {
            phone_number: phone,
            mpesa_amount: mpesaAmount,
            cash_amount: cashAmount,
            discount: discount,
            customer_id: customerId || null,
        };
        // Merge serial numbers
        Object.keys(serialData).forEach(k => { payload['serial_numbers[' + k + ']'] = serialData[k]; });

        fetch('{{ route("pos.mpesaPush") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok || !data.success) throw new Error(data.message || 'STK Push failed');
            return data;
        })
        .then(data => {
            mpesaOrderId = data.order_id;
            // Start polling
            mpesaPollInterval = setInterval(pollStatus, 5000);
            // Show manual confirm button after 30 seconds
            setTimeout(() => {
                document.getElementById('mpesaManualConfirmBox').classList.remove('d-none');
            }, 30000);
        })
        .catch(err => {
            document.getElementById('mpesaFailedMsg').textContent = err.message;
            document.getElementById('mpesaWaitingState').classList.add('d-none');
            document.getElementById('mpesaFailedState').classList.remove('d-none');
        });
    }

    function pollStatus() {
        if (!mpesaOrderId) return;
        fetch('/api/mpesa/checkStatus/' + mpesaOrderId)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    clearInterval(mpesaPollInterval);
                    document.getElementById('mpesaWaitingState').classList.add('d-none');
                    document.getElementById('mpesaSuccessState').classList.remove('d-none');
                    setTimeout(() => {
                        window.location.href = '{{ url("/pos/receipt") }}/' + mpesaOrderId;
                    }, 1500);
                } else if (data.status === 'failed') {
                    clearInterval(mpesaPollInterval);
                    document.getElementById('mpesaFailedMsg').textContent = 'Payment was cancelled or failed. Please try again.';
                    document.getElementById('mpesaWaitingState').classList.add('d-none');
                    document.getElementById('mpesaFailedState').classList.remove('d-none');
                }
                // else still pending — keep polling
            })
            .catch(err => console.error('Poll error:', err));
    }

    function loadCart() {
        fetch('{{ route("pos.cart") }}')
            .then(res => res.json())
            .then(data => renderCart(data));
    }

    function addToCart(productId) {
        fetch('{{ route("pos.addItem") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ product_id: productId, quantity: 1 })
        })
        .then(async res => {
            if(!res.ok) {
                const text = await res.text();
                throw new Error("HTTP " + res.status + " " + text);
            }
            return res.json();
        })
        .then(data => renderCart(data))
        .catch(err => {
            console.error(err);
            alert('Error adding to cart');
        });
    }

    function removeFromCart(productId) {
        fetch('{{ route("pos.removeItem") }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ product_id: productId })
        })
        .then(res => res.json())
        .then(data => renderCart(data))
        .catch(err => console.error(err));
    }

    function emptyCart() {
        if(!confirm('Are you sure you want to empty the cart?')) return;
        fetch('{{ route("pos.emptyCart") }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(() => renderCart({items: {}, total: 0}))
        .catch(err => console.error(err));
    }

    function renderCart(cartData) {
        const tbody = document.getElementById('cartTableBody');
        tbody.innerHTML = '';
        
        let items = Object.values(cartData.items || {});
        cartTotal = cartData.total || 0;

        document.getElementById('cartSubtotal').textContent = `KES ${cartTotal.toFixed(2)}`;
        updateGrandTotal();

        if (items.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">Cart is empty</td></tr>';
            document.getElementById('checkoutBtn').disabled = true;
            return;
        }

        document.getElementById('checkoutBtn').disabled = false;

        items.forEach(item => {
            let serialCell = '<span class="text-muted small">N/A</span>';
            if (item.needs_serial) {
                const serials = availableSerials[item.id] || [];
                let opts = '<option value="">-- Select --</option>';
                serials.forEach(s => { opts += `<option value="${s.id}">${s.sn}</option>`; });
                serialCell = `<select name="serial_numbers[${item.id}]" class="form-select form-select-sm" required style="min-width:120px;">${opts}</select>`;
            }
            let tr = document.createElement('tr');
            tr.innerHTML = `
                <td><small>${item.name}</small><br><small class="text-muted">KES ${parseFloat(item.subtotal).toFixed(2)}</small></td>
                <td>${item.qty}</td>
                <td>${parseFloat(item.price).toFixed(2)}</td>
                <td>${serialCell}</td>
                <td><button type="button" class="btn btn-sm text-danger p-0" onclick="removeFromCart(${item.id})"><i class="fas fa-times"></i></button></td>
            `;
            tbody.appendChild(tr);
        });
    }

    function updateGrandTotal() {
        let discount = parseFloat(document.getElementById('cartDiscount').value) || 0;
        let grandTotal = cartTotal - discount;
        if (grandTotal < 0) grandTotal = 0;
        document.getElementById('cartGrandTotal').textContent = `KES ${grandTotal.toFixed(2)}`;
    }

    // Initialize cart on load
    document.addEventListener('DOMContentLoaded', loadCart);
</script>
@endpush
