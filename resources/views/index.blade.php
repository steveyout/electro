@include('partials.header')

@php
    /**
     * Helper function for rendering product cards using your custom routes.
     */
    $renderProductCard = function($product) {
        if (!$product) return '';

        $minP = $product->getTypeInstance()->getMinimalPrice();
        $regP = $product->price;
        $disc = ($regP > 0) ? round((($regP - $minP) / $regP) * 100) : 0;

        // Using your custom route from web.php
        $productUrl = route('shop.home.product', $product->id);

        return '
        <div class="product-item rounded border bg-white h-100">
            <div class="position-relative overflow-hidden p-3">
                ' . ($disc > 0 ? '<div class="discount-badge">' . $disc . '% <br> <span>off</span></div>' : '') . '
                <img src="' . $product->base_image_url . '" class="img-fluid w-100" style="height:180px; object-fit:contain;" alt="' . $product->name . '">
                <div class="product-action">
                    <a class="btn btn-outline-primary btn-square mx-1" href="' . $productUrl . '"><i class="fa fa-eye"></i></a>
                    <a class="btn btn-outline-primary btn-square mx-1 add-cart" id="' . $product->id . '" href="#"><i class="fa fa-shopping-cart"></i></a>
                </div>
            </div>
            <div class="text-center p-3 pt-0">
                <button id="' . $product->id . '" class="btn btn-primary w-100 mb-2 add-cart py-2">Add to cart</button>
                <a class="h6 d-block text-truncate mb-2 text-decoration-none" href="' . $productUrl . '">' . $product->name . '</a>
                <div class="d-flex justify-content-center align-items-center">
                    <span class="text-primary fw-bold me-2">' . core()->currency($minP) . '</span>
                    ' . ($minP < $regP ? '<span class="text-muted text-decoration-line-through small">' . core()->currency($regP) . '</span>' : '') . '
                </div>
            </div>
        </div>';
    };
@endphp

@if($featuredProducts->count() > 0)
    <div class="container-fluid carousel bg-light px-0">
        <div class="row g-0 justify-content-end">
            <div class="col-12 col-lg-7 col-xl-9">
                <div class="header-carousel owl-carousel bg-light py-5">
                    @foreach($featuredProducts as $product)
                        @php $minPrice = $product->getTypeInstance()->getMinimalPrice(); @endphp
                        <div class="row g-0 header-carousel-item align-items-center">
                            <div class="col-xl-6 carousel-img wow fadeInLeft" data-wow-delay="0.1s">
                                <img src="{{ $product->base_image_url }}" class="img-fluid w-100" alt="{{ $product->name }}">
                            </div>
                            <div class="col-xl-6 carousel-content p-4">
                                <h4 class="text-uppercase fw-bold mb-4 wow fadeInRight" data-wow-delay="0.1s" style="letter-spacing: 3px;">
                                    {{ core()->currency($minPrice) }}
                                </h4>
                                <h1 class="display-3 text-capitalize mb-4 wow fadeInRight" data-wow-delay="0.3s">{{ $product->name }}</h1>
                                <p class="text-dark wow fadeInRight" data-wow-delay="0.5s">Terms and Condition Apply</p>
                                <a class="btn btn-primary rounded-pill py-3 px-5 wow fadeInRight" data-wow-delay="0.7s"
                                   href="{{ route('shop.home.product', $product->id) }}">Shop Now</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            @php $firstProduct = $featuredProducts->first(); @endphp
            @if($firstProduct)
                <div class="col-12 col-lg-5 col-xl-3 wow fadeInRight" data-wow-delay="0.1s">
                    <div class="carousel-header-banner h-100">
                        <img src="{{ $firstProduct->base_image_url }}" class="img-fluid w-100 h-100" style="object-fit: cover;" alt="Banner">
                        <div class="carousel-banner-offer">
                            @php
                                $minP = $firstProduct->getTypeInstance()->getMinimalPrice();
                                $regP = $firstProduct->price;
                                $discount = $regP - $minP;
                            @endphp
                            <p class="bg-primary text-white rounded fs-5 py-2 px-4 mb-0 me-3">
                                Save {{ core()->currency($discount > 0 ? $discount : 0) }}
                            </p>
                            <p class="text-primary fs-5 fw-bold mb-0">Special Offer</p>
                        </div>
                        <div class="carousel-banner">
                            <div class="carousel-banner-content text-center p-4">
                                <a href="#" class="d-block mb-2 text-white">{{ core()->currency($minP) }}</a>
                                <a href="#" class="d-block text-white fs-3">{{ $firstProduct->name }}</a>
                                <span class="text-primary fs-5">{{ core()->currency($minP) }}</span>
                            </div>
                            <a href="{{ route('shop.home.product', $firstProduct->id) }}" class="btn btn-primary rounded-pill py-2 px-4">
                                <i class="fas fa-shopping-cart me-2"></i> Shop Now
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif

<div class="container-fluid px-0">
    <div class="row g-0">
        @php
            $services = [
                ['icon' => 'fa-sync-alt', 'title' => 'Free Return', 'desc' => '30 days money back guarantee!'],
                ['icon' => 'fab fa-telegram-plane', 'title' => 'Free Shipping', 'desc' => 'Free shipping on all order'],
                ['icon' => 'fas fa-life-ring', 'title' => 'Support 24/7', 'desc' => 'We support online 24 hrs a day'],
                ['icon' => 'fas fa-credit-card', 'title' => 'Receive Gift Card', 'desc' => 'Recieve gift all over oder $50'],
                ['icon' => 'fas fa-lock', 'title' => 'Secure Payment', 'desc' => 'We Value Your Security'],
                ['icon' => 'fas fa-blog', 'title' => 'Online Service', 'desc' => 'Free return products in 30 days']
            ];
        @endphp
        @foreach($services as $index => $service)
            <div class="col-6 col-md-4 col-lg-2 border-end wow fadeInUp" data-wow-delay="{{ 0.1 * ($index + 1) }}s">
                <div class="p-4 text-center">
                    <i class="{{ $service['icon'] }} fa-2x text-primary mb-2"></i>
                    <h6 class="text-uppercase mb-1" style="font-size: 12px;">{{ $service['title'] }}</h6>
                    <p class="mb-0 small" style="font-size: 10px;">{{ $service['desc'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="container-fluid product py-5">
    <div class="container-fluid px-lg-5">
        <div class="tab-class">
            <div class="row g-4 align-items-center mb-4">
                <div class="col-lg-4 text-start"><h1>Our Products</h1></div>
                <div class="col-lg-8 text-end">
                    <ul class="nav nav-pills d-inline-flex text-center">
                        <li class="nav-item"><a class="d-flex mx-2 py-2 bg-light rounded-pill active" data-bs-toggle="pill" href="#tab-1"><span class="text-dark" style="width: 130px;">All Products</span></a></li>
                        <li class="nav-item"><a class="d-flex py-2 mx-2 bg-light rounded-pill" data-bs-toggle="pill" href="#tab-2"><span class="text-dark" style="width: 130px;">New Arrivals</span></a></li>
                    </ul>
                </div>
            </div>
            <div class="tab-content">
                @php $tabs = ['tab-1' => $products, 'tab-2' => $newProducts]; @endphp
                @foreach($tabs as $id => $collection)
                    <div id="{{ $id }}" class="tab-pane fade show p-0 {{ $loop->first ? 'active' : '' }}">
                        <div class="row g-3">
                            @foreach($collection->take(10) as $product)
                                <div class="col-6 col-md-4 col-lg-2-4">
                                    {!! $renderProductCard($product) !!}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="container-fluid products py-5 bg-light">
    <div class="container-fluid px-lg-5">
        <div class="mx-auto text-center mb-5" style="max-width: 900px;">
            <h4 class="text-primary border-bottom border-primary border-2 d-inline-block p-2">Products</h4>
            <h1 class="mb-0 display-3">All Items</h1>
        </div>
        <div class="productList-carousel owl-carousel pt-4">
            @foreach($products as $product)
                <div class="h-100 px-1">{!! $renderProductCard($product) !!}</div>
            @endforeach
        </div>
    </div>
</div>

{{-- Promo Banner Section --}}
<div class="container-fluid py-5">
    <div class="container-fluid px-lg-5">
        <div class="row g-4">
            {{-- Banner 4 --}}
            <div class="col-12 col-md-4">
                <div class="promo-banner rounded overflow-hidden">
                    {{-- THIS image has NO text in it --}}
                    <img src="{{ asset('themes/shop/electro/images/banner-4.png') }}" alt="Gaming Gear">
                </div>
            </div>

            {{-- Banner 5 --}}
            <div class="col-12 col-md-4">
                <div class="promo-banner rounded overflow-hidden">
                    <img src="{{ asset('themes/shop/electro/images/banner-5.png') }}" alt="Smart Home">
                </div>
            </div>

            {{-- Banner 6 --}}
            <div class="col-12 col-md-4">
                <div class="promo-banner rounded overflow-hidden">
                    <img src="{{ asset('themes/shop/electro/images/banner-6.png') }}" alt="Wearables">
                </div>
            </div>
        </div>
    </div>
</div>


<div class="container-fluid products py-5">
    <div class="container-fluid px-lg-5">
        <div class="mx-auto text-center mb-5" style="max-width: 900px;">
            <h4 class="text-primary border-bottom border-primary border-2 d-inline-block p-2">Newest Products</h4>
            <h1 class="mb-0 display-3">Recently Added</h1>
        </div>
        <div class="productList-carousel owl-carousel pt-4">
            @foreach($newProducts as $product)
                <div class="h-100 px-1">{!! $renderProductCard($product) !!}</div>
            @endforeach
        </div>
    </div>
</div>



@isset($homeCategories)
    @foreach($homeCategories as $category)
        @php
            $categoryProducts = app('Webkul\Product\Repositories\ProductRepository')->getAll([
                'category_id' => $category->id,
            ])->take(10);
        @endphp

        @if($categoryProducts->count() > 0)
            <div class="container-fluid products py-5 {{ $loop->even ? '' : 'bg-light' }}">
                <div class="container-fluid px-lg-5">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="text-start">
                            <h4 class="text-primary border-bottom border-primary border-2 d-inline-block p-2 mb-0" style="font-size: 1rem;">
                                {{ $category->name }}
                            </h4>
                            <h2 class="display-6 mt-2">Shop {{ $category->name }}</h2>
                        </div>
                        <a href="{{ route('shop.home.category', $category->id) }}" class="btn btn-outline-primary rounded-pill px-4">
                            View All <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>

                    <div class="productList-carousel owl-carousel pt-2">
                        @foreach($categoryProducts as $product)
                            <div class="h-100 px-1">
                                {!! $renderProductCard($product) !!}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Promo Banner Section - Appears after the first category --}}
            @if($loop->first)
                <div class="container-fluid py-5">
                    <div class="container-fluid px-lg-5">
                        <div class="row g-4">
                            {{-- Banner 4 --}}
                            <div class="col-12 col-md-4">
                                <div class="promo-banner rounded overflow-hidden">
                                    {{-- THIS image has NO text in it --}}
                                    <img src="{{ asset('themes/shop/electro/images/banner-4.png') }}" alt="Gaming Gear">
                                </div>
                            </div>

                            {{-- Banner 5 --}}
                            <div class="col-12 col-md-4">
                                <div class="promo-banner rounded overflow-hidden">
                                    <img src="{{ asset('themes/shop/electro/images/banner-5.png') }}" alt="Smart Home">
                                </div>
                            </div>

                            {{-- Banner 6 --}}
                            <div class="col-12 col-md-4">
                                <div class="promo-banner rounded overflow-hidden">
                                    <img src="{{ asset('themes/shop/electro/images/banner-6.png') }}" alt="Wearables">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    @endforeach
@endisset

@include('partials.footer')

<style>
    /* Grid and Carousel Styling */
    @media (min-width: 992px) { .col-lg-2-4 { flex: 0 0 auto; width: 20%; } }
    .productList-carousel .owl-stage-outer { padding: 5px 0; }
    .productList-carousel .owl-item { padding: 0 8px; }
    .product-item img { transition: transform 0.5s ease; height: 180px; object-fit: contain; }
    .product-item:hover img { transform: scale(1.08); }

    .discount-badge {
        position: absolute; top: 10px; right: 10px; background: #ffb400; color: #000;
        font-weight: bold; padding: 4px 8px; border-radius: 2px; line-height: 1.1;
        text-align: center; z-index: 10; font-size: 13px;
    }
    .discount-badge span { font-size: 9px; text-transform: uppercase; display: block; }

    .product-action {
        position: absolute; width: 100%; height: 100%; top: 0; left: 0;
        display: flex; align-items: center; justify-content: center;
        background: rgba(255, 255, 255, 0.6); opacity: 0; transition: 0.5s;
    }
    .product-item:hover .product-action { opacity: 1; }
    .btn-square { width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 4px; }

    /* Owl Navigation Arrows */
    .owl-nav .owl-prev, .owl-nav .owl-next {
        position: absolute; top: 50%; transform: translateY(-50%);
        background: var(--bs-primary) !important; color: #fff !important;
        width: 35px; height: 35px; border-radius: 50%; z-index: 15;
    }
    .owl-nav .owl-prev { left: 5px; }
    .owl-nav .owl-next { right: 5px; }

    .display-6 { font-weight: 700; font-size: 1.5rem; color: #333; }
    @media (max-width: 768px) { .display-6 { font-size: 1.2rem; } }
</style>


