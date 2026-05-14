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
                    <span class="fw-bold">#{{ session('last_order_id') ?? 'PENDING' }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Payment Method:</span>
                    <span class="fw-bold text-uppercase">M-Pesa</span>
                </div>
                <div class="d-flex justify-content-between border-top pt-2 mt-2">
                    <span class="h6 mb-0 fw-bold">Total Amount:</span>
                    <span class="h6 mb-0 fw-bold text-primary">
                        {{-- We use a fallback check here --}}
                        @if(session()->has('order_total'))
                            {!! core()->currency(session('order_total')) !!}
                        @else
                            <span class="small text-muted">Calculating...</span>
                        @endif
                    </span>
                </div>
            </div>

            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                <a href="{{ route('shop.home.index') }}" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm">
                    CONTINUE SHOPPING
                </a>

                {{-- Robust Route Check for 'VIEW ORDER' --}}
                @if (Route::has('customer.orders.index') && auth()->guard('customer')->check())
                    <a href="{{ route('customer.orders.index') }}" class="btn btn-outline-dark btn-lg rounded-pill px-5 fw-bold">
                        VIEW ORDERS
                    </a>
                @else
                    <a href="{{ url('/') }}" class="btn btn-outline-dark btn-lg rounded-pill px-5 fw-bold">
                        RETURN HOME
                    </a>
                @endif
            </div>

            <div class="mt-5 p-3 border rounded-3 bg-white">
                <p class="small text-muted mb-0">
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    A confirmation email has been sent. For any queries, contact our support at <strong>+254 721 966663</strong>.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    .success-icon-wrapper {
        width: 100px;
        height: 100px;
        background: #ffffff;
        border: 5px solid #ff6600;
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

    .text-primary { color: #ff6600 !important; }
    .btn-primary { background-color: #ff6600 !important; border-color: #ff6600 !important; }
    .btn-primary:hover { background-color: #e65c00 !important; }
</style>

@include('partials.footer')
