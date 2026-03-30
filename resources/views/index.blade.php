@include('partials.header')

@php
    /**
     * Helper function for rendering product cards with responsive pricing.
     */
    $renderProductCard = function($product) {
        if (!$product) return '';

        $minP = $product->getTypeInstance()->getMinimalPrice();
        $regP = $product->price;
        $disc = ($regP > 0) ? round((($regP - $minP) / $regP) * 100) : 0;

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


                <div class="d-flex flex-column flex-md-row justify-content-center align-items-center">
                    <span class="text-primary fw-bold me-md-2 mb-1 mb-md-0">' . core()->currency($minP) . '</span>
                    ' . ($minP < $regP ? '<span class="text-muted text-decoration-line-through small">' . core()->currency($regP) . '</span>' : '') . '
                </div>
            </div>
        </div>';
    };
@endphp

{{-- Main Hero Carousel --}}
@if($featuredProducts->count() > 0)
    <div class="container carousel bg-light my-5 rounded shadow-sm">
        <div class="row g-0">
            <div class="col-12">
                <div class="header-carousel owl-carousel bg-light py-5">
                    @foreach($featuredProducts as $product)
                        @php $minPrice = $product->getTypeInstance()->getMinimalPrice(); @endphp
                        <div class="header-carousel-item p-4">
                            <div class="row align-items-center">
                                {{-- Text Section --}}
                                <div class="col-md-6 carousel-content text-start ps-lg-5">
                                    <h5 class="text-muted fw-light mb-2">
                                        Save Up To <span class="text-primary fw-bold">{{ core()->currency($product->price - $minPrice) }}</span> On
                                    </h5>
                                    <h2 class="fw-bold text-dark mb-3">
                                        {{ $product->name }}
                                    </h2>
                                    {{-- Render HTML description --}}
                                    <div class="text-secondary mb-4 small-desc">
                                        {!! $product->short_description !!}
                                    </div>
                                    <a class="btn btn-primary rounded-pill py-2 px-5 fw-bold"
                                       href="{{ route('shop.home.product', $product->id) }}">
                                        Shop Now
                                    </a>
                                </div>
                                {{-- Image Section --}}
                                <div class="col-md-6 carousel-img text-center position-relative">
                                    <div class="bg-circle-overlay"></div>
                                    <img src="{{ $product->base_image_url }}"
                                         class="img-fluid position-relative"
                                         style="max-height: 380px; object-fit: contain; z-index: 2;"
                                         alt="{{ $product->name }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Services Section --}}
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

{{-- Featured Products Tabs --}}
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

{{-- Product Carousel Section --}}
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

{{-- Static Promo Banners --}}
<div class="container-fluid py-5">
    <div class="container-fluid px-lg-5">
        <div class="row g-4">
            <div class="col-12 col-md-4">
                <div class="promo-banner rounded overflow-hidden shadow-sm">
                    <img src="{{ asset('themes/shop/electro/images/banner-4.png') }}" class="img-fluid w-100" alt="Gaming Gear">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="promo-banner rounded overflow-hidden shadow-sm">
                    <img src="{{ asset('themes/shop/electro/images/banner-5.png') }}" class="img-fluid w-100" alt="Smart Home">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="promo-banner rounded overflow-hidden shadow-sm">
                    <img src="{{ asset('themes/shop/electro/images/banner-6.png') }}" class="img-fluid w-100" alt="Wearables">
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Dynamic Category Sections --}}
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
        @endif
    @endforeach
@endisset

@include('partials.footer')

<style>
    /* Global Container Control */
    .container.carousel { max-width: 1200px; overflow: hidden; }

    /* Product Grid & Card Styling */
    @media (min-width: 992px) { .col-lg-2-4 { flex: 0 0 auto; width: 20%; } }
    .product-item img { transition: transform 0.5s ease; height: 180px; object-fit: contain; }
    .product-item:hover img { transform: scale(1.08); }

    /* Pricing Mobile Fix */
    @media (max-width: 767px) {
        .product-item .d-flex.flex-column { gap: 2px; }
        .product-item .text-decoration-line-through { font-size: 0.75rem; opacity: 0.8; }
        .product-item .h6 { font-size: 0.85rem; margin-bottom: 5px !important; }
    }

    /* Carousel Content Styling */
    .carousel-content h2 { font-size: 2rem; letter-spacing: -0.5px; }
    .small-desc { font-size: 0.95rem; color: #6c757d; max-width: 450px; line-height: 1.6; }
    .bg-circle-overlay {
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
        width: 320px; height: 320px; background: radial-gradient(circle, #ffffff 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%; z-index: 1;
    }

    /* Discount Badge */
    .discount-badge {
        position: absolute; top: 10px; right: 10px; background: #ffb400; color: #000;
        font-weight: bold; padding: 4px 8px; border-radius: 2px; line-height: 1.1;
        text-align: center; z-index: 10; font-size: 13px;
    }
    .discount-badge span { font-size: 9px; text-transform: uppercase; display: block; }

    /* Action Buttons */
    .product-action {
        position: absolute; width: 100%; height: 100%; top: 0; left: 0;
        display: flex; align-items: center; justify-content: center;
        background: rgba(255, 255, 255, 0.6); opacity: 0; transition: 0.5s;
    }
    .product-item:hover .product-action { opacity: 1; }
    .btn-square { width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 4px; }

    /* Promo Banner Shadows */
    .promo-banner { transition: transform 0.3s ease; }
    .promo-banner:hover { transform: translateY(-5px); }
</style>


