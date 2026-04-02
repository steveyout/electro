@include('partials.header')

<div class="container-fluid py-5 bg-light">
    <div class="container">
        <h2 class="mb-4 fw-bold">Shopping <span class="text-primary">Cart</span></h2>

        <div class="row g-4">
            {{-- Left Side: Item List --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="table-responsive p-3">
                        <table class="table align-middle">
                            <thead class="text-muted small uppercase">
                            <tr>
                                <th class="border-0">Product</th>
                                <th class="border-0">Price</th>
                                <th class="border-0">Qty</th>
                                <th class="border-0 text-end">Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php $cart = Webkul\Checkout\Facades\Cart::getCart(); @endphp
                            @if($cart && $cart->items->count() > 0)
                                @foreach($cart->items as $item)
                                    <tr>
                                        <td class="py-3">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $item->product->base_image_url }}" class="rounded border" style="width: 70px; height: 70px; object-fit: contain;">
                                                <div class="ms-3">
                                                    <h6 class="mb-0 fw-bold text-dark">{{ $item->name }}</h6>
                                                    <button class="btn btn-link btn-sm text-danger p-0 mt-1 remove-item" data-id="{{ $item->id }}">
                                                        <i class="fas fa-trash-alt me-1"></i>Remove
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ core()->currency($item->base_price) }}</td>
                                        <td style="width: 120px;">
                                            <div class="input-group input-group-sm border rounded-pill">
                                                <button class="btn border-0 update-qty" data-type="minus">-</button>
                                                <input type="number"
                                                       name="qty[{{ $item->id }}]"
                                                       value="{{ $item->quantity }}"
                                                       class="form-control text-center">
                                                <button class="btn border-0 update-qty" data-type="plus">+</button>
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold text-primary">
                                            {{ core()->currency($item->base_total) }}
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <i class="fas fa-shopping-basket fa-4x text-light mb-3"></i>
                                        <p class="text-muted">Your cart is empty.</p>
                                        <a href="{{ route('shop.home.index') }}" class="btn btn-primary rounded-pill px-4">Shop Now</a>
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Right Side: Summary --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 p-4">
                    <h5 class="fw-bold mb-4">Order Summary</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span class="fw-bold">{{ $cart ? core()->currency($cart->base_sub_total) : '0.00' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span class="text-muted">Tax</span>
                        <span class="fw-bold">{{ $cart ? core()->currency($cart->base_tax_total) : '0.00' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="h5 fw-bold">Total</span>
                        <span class="h5 fw-bold text-primary">{{ $cart ? core()->currency($cart->base_grand_total) : '0.00' }}</span>
                    </div>
                    <a href="{{ route('shop.checkout.onepage.index') }}" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm">
                        PROCEED TO CHECKOUT
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials.footer')
