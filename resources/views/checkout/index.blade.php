@include('partials.header')

<div class="container py-5">
    <div class="row g-5">
        {{-- Billing & Shipping Details --}}
        <div class="col-lg-7">
            <div class="d-flex align-items-center mb-4">
                <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">1</span>
                <h4 class="mb-0 fw-bold">Shipping Details</h4>
            </div>

            <form id="checkout-form" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">First Name</label>
                    <input type="text" class="form-control rounded-3" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">Last Name</label>
                    <input type="text" class="form-control rounded-3" required>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-bold text-muted">Street Address</label>
                    <input type="text" class="form-control rounded-3" placeholder="House number and street name" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">Town / City</label>
                    <input type="text" class="form-control rounded-3" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-muted">Phone Number</label>
                    <input type="tel" class="form-control rounded-3" placeholder="+254..." required>
                </div>
            </form>

            <div class="mt-5">
                <div class="d-flex align-items-center mb-4">
                    <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">2</span>
                    <h4 class="mb-0 fw-bold">Payment Method</h4>
                </div>

                {{-- Pay on Delivery Option --}}
                <div class="border rounded-3 p-3 bg-white mb-3 shadow-sm border-orange-hover">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment[method]" id="cod" value="cashondelivery" checked>
                        <label class="form-check-label fw-bold d-flex align-items-center" for="cod">
                            <i class="fas fa-hand-holding-usd me-2 text-primary"></i>
                            Pay on Delivery
                        </label>
                        <p class="small text-muted mb-0 ms-4">Pay with cash or M-Pesa upon receiving your items.</p>
                    </div>
                </div>

                {{-- Direct M-Pesa Option (Optional) --}}
                <div class="border rounded-3 p-3 bg-white shadow-sm">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment[method]" id="mpesa" value="mpesa">
                        <label class="form-check-label fw-bold d-flex align-items-center" for="mpesa">
                            <i class="fas fa-mobile-alt me-2 text-success"></i>
                            M-Pesa Express (STK Push)
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Order Summary Sidebar --}}
        <div class="col-lg-5">
            <div class="bg-light p-4 rounded-3 border sticky-top" style="top: 100px;">
                <h5 class="fw-bold mb-4">Your Order</h5>
                <div class="mb-4">
                    @foreach($cart->items as $item)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">{{ $item->name }} <strong class="text-dark">x {{ $item->quantity }}</strong></span>
                            <span class="small fw-bold">{{ core()->currency($item->base_total) }}</span>
                        </div>
                    @endforeach
                </div>
                <hr>
                <div class="d-flex justify-content-between h5 fw-bold mt-3">
                    <span>Total</span>
                    <span class="text-primary">{{ core()->currency($cart->base_grand_total) }}</span>
                </div>
                <button type="submit" form="checkout-form" class="btn btn-primary w-100 py-3 rounded-pill fw-bold mt-4 shadow">
                    PLACE ORDER NOW
                </button>
            </div>
        </div>
    </div>
</div>

@include('partials.footer')
<script>
    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        e.preventDefault();

        // Simple UI feedback
        const btn = e.target.querySelector('button[type="submit"]');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing...';
        btn.disabled = true;

        // This is a simplified logic. Bagisto usually requires:
        // 1. Save Address -> 2. Save Shipping -> 3. Save Payment -> 4. Save Order

        // For your custom theme, you can hit your own route:
        const formData = new FormData(this);

        fetch("{{ route('shop.checkout.save_order') }}", {
            method: "POST",
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    window.location.href = "{{ route('shop.checkout.success') }}";
                } else {
                    alert("Something went wrong. Please check your details.");
                    btn.disabled = false;
                    btn.innerText = "PLACE ORDER NOW";
                }
            });
    });
</script>
