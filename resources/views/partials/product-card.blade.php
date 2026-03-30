<div class="col-md-6 col-lg-4 col-xl-3">
    <div class="product-item rounded wow fadeInUp" data-wow-delay="0.1s">
        @php
            $minPrice = $product->getTypeInstance()->getMinimalPrice();
            $basePrice = $product->price;
        @endphp
        <div class="product-item-inner border rounded">
            <div class="product-item-inner-item">
                <img src="{{ $product->base_image_url }}" class="img-fluid w-100 rounded-top" alt="">
                @if($product->new) <div class="product-new small">New</div> @endif
                <div class="product-details">
                    <a href="{{ route('shop.home.product', $product->id) }}"><i class="fa fa-eye fa-1x"></i></a>
                </div>
            </div>
            <div class="text-center rounded-bottom p-3">
                <a href="{{ route('shop.home.product', $product->id) }}" class="d-block h6 text-truncate mb-2" style="font-size: 0.95rem;">{{ $product->name }}</a>
                <div class="mb-0">
                    @if($minPrice < $basePrice)
                        <del class="me-2 text-muted small" style="font-size: 0.8rem;">{{ core()->currency($basePrice) }}</del>
                    @endif
                    <span class="text-primary fw-bold" style="font-size: 0.9rem;">{{ core()->currency($minPrice) }}</span>
                </div>
            </div>
        </div>
        <div class="product-item-add border border-top-0 rounded-bottom text-center p-3 pt-0">
            <button id="{{ $product->id }}" class="btn btn-primary border-secondary rounded-pill py-1 px-4 mb-3 add-cart small" style="font-size: 0.85rem;">
                <i class="fas fa-shopping-cart me-2"></i> Add To Cart
            </button>
        </div>
    </div>
</div>
