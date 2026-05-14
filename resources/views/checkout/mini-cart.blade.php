<div class="offcanvas offcanvas-end" tabindex="-1" id="miniCartDrawer" aria-labelledby="miniCartLabel">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="miniCartLabel"><i class="fas fa-shopping-cart me-2"></i>Your Shopping Cart</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div id="mini-cart-list">
            @if($cart && $cart->items->count() > 0)
                @foreach($cart->items as $item)
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom cart-item-row">
                        <div class="flex-shrink-0">
                            <img src="{{ $item->product->base_image_url }}" alt="{{ $item->name }}" class="img-fluid rounded" style="width: 70px; height: 70px; object-fit: cover;">
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 small fw-bold">{{ $item->name }}</h6>
                            <p class="mb-0 text-muted small">{{ $item->quantity }} x {!! core()->currency($item->base_price) !!}</p>
                        </div>
                        <button class="btn btn-sm text-danger remove-cart-item" data-id="{{ $item->id }}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                @endforeach
            @else
                <div class="text-center py-5">
                    <i class="fas fa-shopping-basket fa-4x text-light mb-3"></i>
                    <p class="text-muted">Your cart is currently empty.</p>
                    <button class="btn btn-primary btn-sm rounded-pill px-4" data-bs-dismiss="offcanvas">Start Shopping</button>
                </div>
            @endif
        </div>
    </div>
    <div class="offcanvas-footer p-4 border-top">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <span class="h6 mb-0 fw-bold">Total:</span>
            <span class="h5 mb-0 fw-bold text-primary cart-total-display">
                {!! $cart ? core()->currency($cart->base_grand_total) : core()->currency(0) !!}
            </span>
        </div>
        <div class="d-grid gap-2">
            <a href="{{ route('shop.checkout.cart.index') }}" class="btn btn-outline-primary rounded-pill">View Full Cart</a>
            <a href="{{ route('shop.checkout.onepage.index') }}" class="btn btn-primary rounded-pill">Proceed to Checkout</a>
        </div>
    </div>
</div>
