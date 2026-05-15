@include('partials.header')

<div class="container py-5">
    <div class="row g-5">
        {{-- Checkout Details --}}
        <div class="col-lg-7">
            <div class="d-flex align-items-center mb-4">
                <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">1</span>
                <h4 class="mb-0 fw-bold">Shipping & Contact</h4>
            </div>

            <form id="checkout-form" class="row g-3">
                @csrf
                {{-- Names --}}
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">First Name</label>
                    <input type="text" name="billing[first_name]" class="form-control rounded-3" placeholder="e.g. Stephen" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">Last Name</label>
                    <input type="text" name="billing[last_name]" class="form-control rounded-3" placeholder="e.g. Githire" required>
                </div>

                {{-- Email for Guest Order Tracking --}}
                <div class="col-12">
                    <label class="form-label small fw-bold text-muted">Email Address</label>
                    <input type="email" name="billing[email]" class="form-control rounded-3" placeholder="yourname@example.com" required>
                </div>

                {{-- Address --}}
                <div class="col-12">
                    <label class="form-label small fw-bold text-muted">Delivery Address / Building</label>
                    <input type="text" name="billing[address1][]" class="form-control rounded-3" placeholder="e.g. Jengi Plaza, Limuru" required>
                </div>

                {{-- Location --}}
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">Town / City</label>
                    <input type="text" name="billing[city]" class="form-control rounded-3" value="Nairobi" required>
                </div>

                {{-- Phone for M-Pesa --}}
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">M-Pesa Phone Number</label>
                    <input type="tel" name="billing[phone]" class="form-control rounded-3" placeholder="0712345678" required>
                </div>

                {{-- Hidden Bagisto Requirements --}}
                <input type="hidden" name="billing[country]" value="KE">
                <input type="hidden" name="billing[state]" value="KE">
                <input type="hidden" name="billing[postcode]" value="00100">
                <input type="hidden" name="billing[use_for_shipping]" value="true">

                {{-- M-Pesa is the only method --}}
                <input type="hidden" name="payment[method]" value="mpesa">
                <input type="hidden" name="shipping_method" value="flatrate_flatrate">
            </form>

            <div class="mt-5">
                <div class="d-flex align-items-center mb-4">
                    <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">2</span>
                    <h4 class="mb-0 fw-bold">Payment Method</h4>
                </div>

                <div class="border rounded-3 p-4 bg-white shadow-sm border-success" style="border-width: 2px;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <label class="fw-bold d-flex align-items-center h5 mb-1">
                                <i class="fas fa-mobile-alt me-2 text-success"></i>
                                M-Pesa Express
                            </label>
                            <p class="small text-muted mb-0">You will receive an STK Push on your phone to enter your PIN.</p>
                        </div>
                        <img src="https://upload.wikimedia.org/wikipedia/commons/1/15/M-PESA_LOGO-01.svg" alt="Mpesa" style="height: 40px;">
                    </div>
                </div>
            </div>
        </div>

        {{-- Order Summary --}}
        <div class="col-lg-5">
            <div class="bg-light p-4 rounded-3 border sticky-top" style="top: 100px;">
                <h5 class="fw-bold mb-4">Summary</h5>
                <div class="mb-4" style="max-height: 300px; overflow-y: auto;">
                    @foreach($cart->items as $item)
                        <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                            <div>
                                <span class="d-block fw-bold small text-dark">{{ $item->name }}</span>
                                <span class="text-muted extra-small">Qty: {{ $item->quantity }}</span>
                            </div>
                            <span class="small fw-bold">{{ core()->currency($item->base_total) }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span>{{ core()->currency($cart->base_sub_total) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Shipping</span>
                    <span>{{ core()->currency($cart->base_shipping_amount) }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between h4 fw-bold mt-3">
                    <span>Total</span>
                    <span class="text-primary">{{ core()->currency($cart->base_grand_total) }}</span>
                </div>

                <button type="submit" id="submit-btn" form="checkout-form" class="btn btn-primary w-100 py-3 rounded-pill fw-bold mt-4 shadow-lg text-uppercase">
                    Place Order
                </button>

                <div class="text-center mt-3">
                    <small class="text-muted"><i class="fas fa-lock me-1"></i> Secure Encrypted Payment</small>
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials.footer')

<script>
    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const btn = document.getElementById('submit-btn');
        const originalText = btn.innerHTML;

        // UI State: Loading
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Initializing Order...';
        btn.disabled = true;

        const formData = new FormData(this);

        fetch("{{ route('shop.checkout.save_order') }}", {
            method: "POST",
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
            .then(async response => {
                const data = await response.json();

                if (data.status) {
                    // Success: Redirect to success page (M-Pesa logic usually happens after redirect or via popup)
                    window.location.href = data.redirect_url;
                } else {
                    throw new Error(data.message || "Checkout failed");
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Error: " + error.message);
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
    });
</script>

<style>
    .extra-small { font-size: 0.75rem; }
    .border-orange-hover:hover { border-color: #ff5722 !important; cursor: pointer; }
    .spinner-border-sm { width: 1rem; height: 1rem; }
</style>
