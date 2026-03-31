@include('partials.header')

@php
    /**
     * Helper function for rendering product cards with responsive pricing.
     */
    $renderProductCard = function($item) {
        if (!$item) return '';

        $minP = $item->getTypeInstance()->getMinimalPrice();
        $regP = $item->price;
        $disc = ($regP > 0 && $regP > $minP) ? round((($regP - $minP) / $regP) * 100) : 0;
        $productUrl = route('shop.home.product', $item->id);

        return '
        <div class="product-item rounded border bg-white h-100 shadow-sm">
            <div class="position-relative overflow-hidden p-3">
                ' . ($disc > 0 ? '<div class="discount-badge">' . $disc . '% <br> <span>off</span></div>' : '') . '
                <img src="' . $item->base_image_url . '" class="img-fluid w-100" style="height:150px; object-fit:contain;" alt="' . $item->name . '">
                <div class="product-action">
                    <a class="btn btn-outline-primary btn-square mx-1" href="' . $productUrl . '"><i class="fa fa-eye"></i></a>
                    <a class="btn btn-outline-primary btn-square mx-1 add-to-cart-btn" data-id="' . $item->id . '" data-qty="1" href="javascript:void(0);"><i class="fa fa-shopping-cart"></i></a>
                </div>
            </div>
            <div class="text-center p-3 pt-0">
                <a class="h6 d-block text-truncate mb-2 text-decoration-none text-dark" href="' . $productUrl . '">' . $item->name . '</a>
                <div class="d-flex flex-column justify-content-center align-items-center mb-2">
                    <span class="text-primary fw-bold mb-0">' . core()->currency($minP) . '</span>
                    ' . ($disc > 0 ? '<span class="text-muted text-decoration-line-through small" style="font-size: 0.8rem;">' . core()->currency($regP) . '</span>' : '') . '
                </div>
                <button class="btn btn-primary btn-sm w-100 add-to-cart-btn py-2" data-id="' . $item->id . '" data-qty="1">Add to cart</button>
            </div>
        </div>';
    };

    $mainMinPrice = $product->getTypeInstance()->getMinimalPrice();
    $mainRegPrice = $product->price;

    // WhatsApp Formatting
    $whatsappNumber = "254721966663";
    $whatsappMessage = urlencode("Hello, I would like to order: " . $product->name . " (Price: " . core()->currency($mainMinPrice) . "). Link: " . route('shop.home.product', $product->id));

    // Manually fetch reviews to avoid partial dependency
    $reviewHelper = app('Webkul\Product\Helpers\Review');
    $avgRating = $reviewHelper->getAverageRating($product);
    $totalReviews = $reviewHelper->getTotalReviews($product);
    $reviews = $reviewHelper->getReviews($product)->where('status', 'approved');
@endphp

<style>
    #product-main-carousel { background: #fff; border-radius: 12px; border: 1px solid #eee; overflow: visible !important; }
    .single-carousel .single-item { display: flex; align-items: center; justify-content: center; aspect-ratio: 1 / 1; }
    .single-carousel .single-item img { width: 100%; height: 100%; object-fit: contain; padding: 30px; }
    .price-display .final-price { color: #ff9800 !important; font-size: 2.5rem !important; font-weight: 800 !important; }

    .short-desc-container { font-size: 0.95rem; line-height: 1.7; color: #4a4a4a; }
    .short-desc-container ul { list-style: none; padding-left: 0; }
    .short-desc-container ul li { position: relative; padding-left: 20px; margin-bottom: 8px; }
    .short-desc-container ul li::before { content: "▪"; position: absolute; left: 0; color: #333; font-weight: bold; }

    .discount-badge {
        position: absolute; top: 10px; right: 10px; background: #ffb400; color: #000;
        font-weight: bold; padding: 2px 6px; border-radius: 2px; line-height: 1.1;
        text-align: center; z-index: 10; font-size: 11px;
    }
    .discount-badge span { font-size: 8px; text-transform: uppercase; display: block; }
    .product-action {
        position: absolute; width: 100%; height: 100%; top: 0; left: 0;
        display: flex; align-items: center; justify-content: center;
        background: rgba(255, 255, 255, 0.6); opacity: 0; transition: 0.5s;
    }
    .product-item:hover .product-action { opacity: 1; }
    .btn-square { width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; border-radius: 4px; }

    .btn-whatsapp { background-color: #25D366 !important; color: white !important; border: none !important; font-weight: 600; }
    .btn-loading { opacity: 0.7; pointer-events: none; }
</style>

<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6">{{ $product->name }}</h1>
    <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item"><a href="{{ route('shop.home.index') }}">Home</a></li>
        <li class="breadcrumb-item active text-white">{{ $product->name }}</li>
    </ol>
</div>

<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-3">
                <div class="mb-4">
                    <h4 class="fw-bold">Categories</h4>
                    <ul class="list-unstyled mt-3">
                        @foreach($categories as $category)
                            <li class="mb-2">
                                <a href="{{ url('categories/'.$category->id) }}" class="text-dark text-decoration-none">
                                    <i class="fas fa-chevron-right text-primary small me-2"></i>{{ $category->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div id="product-main-carousel" class="single-carousel owl-carousel owl-theme shadow-sm">
                            @forelse($product->images as $image)
                                <div class="single-item">
                                    <img src="{{ $image->url }}" alt="{{ $product->name }}">
                                </div>
                            @empty
                                <div class="single-item">
                                    <img src="{{ asset('vendor/webkul/ui/assets/images/product-placeholder.webp') }}" alt="Placeholder">
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h2 class="fw-bold mb-1">{{ $product->name }}</h2>
                        <p class="text-muted small mb-3">SKU: {{ $product->sku }}</p>

                        <div class="price-display mb-3">
                            <span class="final-price text-primary fw-bold" style="font-size: 2.2rem;">{{ core()->currency($mainMinPrice) }}</span>
                            @if($mainRegPrice > $mainMinPrice)
                                <span class="text-muted text-decoration-line-through ms-2">{{ core()->currency($mainRegPrice) }}</span>
                            @endif
                        </div>

                        <div class="text-warning mb-3">
                            @for($i=1; $i<=5; $i++)
                                <i class="{{ $i <= $avgRating ? 'fas' : 'far' }} fa-star"></i>
                            @endfor
                            <span class="text-muted small ms-2">({{ $totalReviews }} reviews)</span>
                        </div>

                        <div class="short-desc-container mb-4">
                            {!! $product->short_description !!}
                        </div>

                        <div class="whatsapp-order mb-4">
                            <a href="https://wa.me/{{ $whatsappNumber }}?text={{ $whatsappMessage }}" target="_blank" class="btn btn-whatsapp rounded px-4 py-2 w-100 d-inline-flex align-items-center justify-content-center shadow-sm">
                                <i class="fab fa-whatsapp me-2 fs-5"></i> Order via WhatsApp
                            </a>
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <div class="input-group quantity" style="width: 120px;">
                                <button class="btn btn-sm btn-minus rounded-circle bg-light border"><i class="fa fa-minus"></i></button>
                                <input type="text" id="qty-input" class="form-control form-control-sm text-center border-0" value="1" readonly>
                                <button class="btn btn-sm btn-plus rounded-circle bg-light border"><i class="fa fa-plus"></i></button>
                            </div>
                            <button class="btn btn-primary px-4 py-2 text-white add-to-cart-btn w-100" id="main-add-cart" data-id="{{ $product->id }}">
                                <i class="fa fa-shopping-bag me-2"></i> Add to cart
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-12">
                        <nav>
                            <div class="nav nav-tabs mb-3" id="productTab" role="tablist">
                                <button class="nav-link active fw-bold" data-bs-toggle="tab" data-bs-target="#tab-desc" type="button">Description</button>
                                <button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#tab-reviews" type="button">Reviews ({{ $totalReviews }})</button>
                            </div>
                        </nav>
                        <div class="tab-content border rounded p-4 bg-white shadow-sm mb-5">
                            <div class="tab-pane fade show active" id="tab-desc">
                                {!! $product->description !!}
                            </div>
                            <div class="tab-pane fade" id="tab-reviews">
                                @forelse($reviews as $review)
                                    <div class="d-flex mb-4 border-bottom pb-3">
                                        <div class="flex-shrink-0">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="fa fa-user text-muted"></i>
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-0">{{ $review->name }}</h6>
                                            <div class="text-warning small mb-1">
                                                @for($i=1; $i<=5; $i++) <i class="fa{{ $i <= $review->rating ? 's' : 'r' }} fa-star"></i> @endfor
                                            </div>
                                            <p class="mb-0 text-muted small">{{ $review->comment }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted">No reviews yet for this product.</p>
                                @endforelse
                            </div>
                        </div>

                        @if($relatedProducts->count() > 0)
                            <div class="related-products mt-5 pt-4">
                                <h3 class="fw-bold mb-4">Related Products</h3>
                                <div class="related-carousel owl-carousel">
                                    @foreach($relatedProducts as $related)
                                        <div class="h-100 px-1">
                                            {!! $renderProductCard($related) !!}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials.footer')

<script>
    $(document).ready(function(){
        $("#product-main-carousel").owlCarousel({
            items: 1,
            loop: true,
            nav: true,
            dots: false,
            navText: ["<i class='fa fa-chevron-left'></i>", "<i class='fa fa-chevron-right'></i>"]
        });

        $(".related-carousel").owlCarousel({
            margin: 15,
            loop: true,
            autoplay: true,
            responsive: {
                0: { items: 1 },
                576: { items: 2 },
                768: { items: 3 }
            }
        });
    });
</script>
