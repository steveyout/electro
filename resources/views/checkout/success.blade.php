@include('partials.header')

<div class="container py-5 my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            {{-- Animated Success Icon --}}
            <div class="mb-4">
                <div class="success-icon-wrapper shadow-sm">
                    <i class="fas fa-check"></i>
                </div>
            </div>

            <h1 class="fw-bold mb-2">Order <span class="text-primary">Confirmed!</span></h1>
            <p class="text-muted mb-4 fs-5">Thank you for shopping with us. Your order has been placed successfully and is being processed.</p>

            <div class="card border-0 bg-light rounded-4 p-4 mb-4 text-start">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Order ID:</span>
                    <span class="fw-bold">#{{ session('last_order_id') ?? '10001' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Payment Method:</span>
                    <span class="fw-bold text-uppercase">
                        {{-- Logic to show method name --}}
                        {{ request()->get('method') == 'mpesa' ? 'M-Pesa' : 'Pay on Delivery' }}
                    </span>
                </div>
                <div class="d-flex justify-content-between border-top pt-2 mt-2">
                    <span class="h6 mb-0 fw-bold">Total Amount:</span>
                    <span class="h6 mb-0 fw-bold text-primary">
                        {{-- You can pass the actual total via session or variable --}}
                        {!! core()->currency(session('order_total') ?? 0) !!}
                    </span>
                </div>
            </div>

            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                <a href="{{ route('shop.home.index') }}" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm">
                    CONTINUE SHOPPING
                </a>
                <a href="{{ route('shop.customer.orders.index') }}" class="btn btn-outline-dark btn-lg rounded-pill px-5 fw-bold">
                    VIEW ORDER
                </a>
            </div>

            <div class="mt-5 p-3 border rounded-3 bg-white">
                <p class="small text-muted mb-0">
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    A confirmation email has been sent to your registered address. For any queries, contact our support at <strong>+254 721 966663</strong>.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Success Icon Styling */
    .success-icon-wrapper {
        width: 100px;
        height: 100px;
        background: #ffffff;
        border: 5px solid #ff6600; /* Your Theme Orange */
        color: #ff6600;
        font-size: 50px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        animation: scaleIn 0.5s ease-out;
    }

    @keyframes scaleIn {
        0% { transform: scale(0); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }

    /* Primary color override for your orange theme */
    .text-primary { color: #ff6600 !important; }
    .btn-primary { background-color: #ff6600 !important; border-color: #ff6600 !important; }
    .btn-primary:hover { background-color: #e65c00 !important; }
    .btn-outline-dark:hover { background-color: #333; color: #fff; }
</style>

@include('partials.footer')
