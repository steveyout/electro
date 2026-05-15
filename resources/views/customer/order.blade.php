@include('partials.header')

<div class="container-fluid py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0 fw-bold">Order <span class="text-primary">#{{ $order->increment_id }}</span></h2>
            <a href="{{ route('customer.orders.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i>Back to Orders
            </a>
        </div>

        <div class="row g-4">
            {{-- Left Side: Order Items & Shipping --}}
            <div class="col-lg-8">
                {{-- Items Card --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="mb-0 fw-bold">Items Ordered</h6>
                    </div>
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
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center">
                                            {{-- Using a fallback if product image isn't directly on the item --}}
                                            <img src="{{ $item->product ? $item->product->base_image_url : '' }}"
                                                 class="rounded border"
                                                 style="width: 60px; height: 60px; object-fit: contain;">
                                            <div class="ms-3">
                                                <h6 class="mb-0 fw-bold text-dark">{{ $item->name }}</h6>
                                                @if (isset($item->additional['attributes']))
                                                    <div class="small text-muted">
                                                        @foreach($item->additional['attributes'] as $attribute)
                                                            {{ $attribute['attribute_name'] }}: {{ $attribute['option_label'] }} {{ !$loop->last ? '|' : '' }}
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ core()->formatPrice($item->base_price, $order->order_currency_code) }}</td>
                                    <td>{{ $item->qty_ordered }}</td>
                                    <td class="text-end fw-bold text-primary">
                                        {{ core()->formatPrice($item->base_total, $order->order_currency_code) }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Addresses Row --}}
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-3 h-100">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h6 class="mb-0 fw-bold"><i class="fas fa-map-marker-alt text-primary me-2"></i>Shipping Address</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-0 text-muted small">
                                    <strong>{{ $order->shipping_address->name }}</strong><br>
                                    {{ $order->shipping_address->address1 }}<br>
                                    {{ $order->shipping_address->city }}, {{ $order->shipping_address->state }}<br>
                                    {{ $order->shipping_address->country }} {{ $order->shipping_address->postcode }}<br>
                                    <span class="d-block mt-2">T: {{ $order->shipping_address->phone }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-3 h-100">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h6 class="mb-0 fw-bold"><i class="fas fa-credit-card text-primary me-2"></i>Payment & Shipping</h6>
                            </div>
                            <div class="card-body">
                                <h6 class="small fw-bold mb-1">Method</h6>
                                <p class="text-muted small mb-3">{{ core()->getConfigData('payment.settings.title.' . $order->payment->method) ?? $order->payment->method }}</p>

                                <h6 class="small fw-bold mb-1">Shipping</h6>
                                <p class="text-muted small mb-0">{{ $order->shipping_title }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Side: Order Summary --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 p-4 sticky-top" style="top: 20px;">
                    <h5 class="fw-bold mb-4">Order Summary</h5>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span class="fw-bold">{{ core()->formatPrice($order->base_sub_total, $order->order_currency_code) }}</span>
                    </div>

                    @if($order->base_shipping_amount > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Shipping</span>
                            <span class="fw-bold">+ {{ core()->formatPrice($order->base_shipping_amount, $order->order_currency_code) }}</span>
                        </div>
                    @endif

                    @if($order->base_tax_amount > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tax</span>
                            <span class="fw-bold">{{ core()->formatPrice($order->base_tax_amount, $order->order_currency_code) }}</span>
                        </div>
                    @endif

                    @if($order->base_discount_amount > 0)
                        <div class="d-flex justify-content-between mb-2 text-danger">
                            <span class="">Discount</span>
                            <span class="fw-bold">- {{ core()->formatPrice($order->base_discount_amount, $order->order_currency_code) }}</span>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between mb-4 mt-3 pt-3 border-top">
                        <span class="h5 fw-bold">Total</span>
                        <span class="h5 fw-bold text-primary">{{ core()->formatPrice($order->base_grand_total, $order->order_currency_code) }}</span>
                    </div>

                    <div class="alert alert-light border-0 small text-center mb-0">
                        Order Status: <strong class="text-capitalize text-primary">{{ $order->status }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials.footer')
