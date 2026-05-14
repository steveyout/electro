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

                    <button class="btn btn-outline-primary btn-square mx-1 add-to-cart-btn" data-id="' . $product->id . '">
                        <i class="fa fa-shopping-cart"></i>
                    </button>
                </div>
            </div>
            <div class="text-center p-3 pt-0">

                <button class="btn btn-primary w-100 mb-2 add-to-cart-btn py-2" data-id="' . $product->id . '">
                    Add to cart
                </button>
                <a class="h6 d-block text-truncate mb-2 text-decoration-none" href="' . $productUrl . '">' . $product->name . '</a>

                <div class="d-flex flex-column justify-content-center align-items-center">
                    <span class="text-primary fw-bold mb-0" style="font-size: 1.1rem;">' . core()->currency($minP) . '</span>
                    ' . ($minP < $regP ? '<span class="text-muted text-decoration-line-through" style="font-size: 0.85rem; margin-top: -2px;">' . core()->currency($regP) . '</span>' : '') . '
                </div>
            </div>
        </div>';
    };
@endphp

{{-- Main Hero Section --}}
<div class="container-fluid px-lg-5 my-4">
    <div class="row g-3">
        {{-- Left Sidebar: Categories --}}
        <div class="col-lg-3 d-none d-lg-block">
            <div class="card border-0 shadow-sm rounded overflow-hidden bg-white h-100">
                <div class="list-group list-group-flush category-sidebar">
                    @isset($homeCategories)
                        @foreach($homeCategories as $category)
                            <div class="category-item border-bottom">
                                <a href="{{ route('shop.home.category', $category->id) }}"
                                   class="list-group-item list-group-item-action border-0 py-2 d-flex justify-content-between align-items-center">
                                    <span class="small">
                                        <i class="fas fa-list-ul me-2 text-muted" style="width: 15px;"></i>
                                        {{ $category->name }}
                                    </span>
                                    <i class="fas fa-chevron-right text-muted" style="font-size: 10px;"></i>
                                </a>

                                {{-- Hover Sub-categories --}}
                                @if($category->children->count() > 0)
                                    <div class="category-submenu shadow-lg rounded-end">
                                        <div class="p-4">
                                            <h6 class="text-primary border-bottom pb-2 fw-bold">{{ $category->name }}</h6>
                                            <div class="row">
                                                @foreach($category->children as $child)
                                                    <div class="col-6 mb-2">
                                                        <a href="{{ route('shop.home.category', $child->id) }}" class="text-decoration-none text-dark small hover-orange">
                                                            {{ $child->name }}
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endisset
                </div>
            </div>
        </div>

        {{-- Center: Hero Carousel --}}
        <div class="col-lg-7 col-md-8 col-12">
            @if($featuredProducts->count() > 0)
                <div class="shadow-sm rounded overflow-hidden h-100" style="background: #F4F6F7;">
                    <div class="header-carousel owl-carousel py-0">
                        @foreach($featuredProducts as $product)
                            @php
                                $minPrice = $product->getTypeInstance()->getMinimalPrice();
                                $displayDesc = \Illuminate\Support\Str::limit(strip_tags($product->short_description), 100);
                            @endphp
                            <div class="header-carousel-item" style="width: 100%;">
                                <div class="row g-0 align-items-center">
                                    <div class="col-md-7 carousel-content text-start p-4 p-lg-5">
                                        <h5 class="text-muted fw-light mb-2" style="font-size: 0.9rem;">
                                            Save Up To <span class="text-primary fw-bold">{{ core()->currency($product->price - $minPrice) }}</span>
                                        </h5>
                                        <h2 class="fw-bold text-dark mb-3">
                                            {{ $product->name }}
                                        </h2>
                                        <div class="text-secondary mb-4 d-none d-md-block" style="font-size: 0.85rem;">
                                            {!! $displayDesc !!}
                                        </div>
                                        <a class="btn btn-primary rounded-pill py-2 px-4 fw-bold"
                                           href="{{ route('shop.home.product', $product->id) }}">
                                            Shop Now
                                        </a>
                                    </div>

                                    <div class="col-md-5 text-center p-3 position-relative">
                                        <img src="{{ $product->base_image_url }}"
                                             class="img-fluid hero-img"
                                             style="height: 320px; object-fit: contain; width: 100%;"
                                             alt="{{ $product->name }}">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Right Side: Static Promo Cards --}}
        <div class="col-lg-2 col-md-4 d-none d-md-block">
            <div class="d-flex flex-column h-100 gap-3">
                <div class="promo-card shadow-sm rounded overflow-hidden bg-white flex-fill">
                    <img src="{{ asset('themes/shop/electro/images/banner-4.png') }}" class="img-fluid h-100 w-100" style="object-fit: cover;" alt="Promo">
                </div>
                <div class="promo-card shadow-sm rounded overflow-hidden bg-white flex-fill">
                    <img src="{{ asset('themes/shop/electro/images/banner-5.png') }}" class="img-fluid h-100 w-100" style="object-fit: cover;" alt="Promo">
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Category Slider Section (Jumia Style) --}}
<div class="container-fluid px-lg-5 mb-5">
    <div class="category-slider-wrapper p-3 rounded shadow-sm" style="background: #73C2D9;">
        <div class="category-carousel owl-carousel">
            @isset($homeCategories)
                @foreach($homeCategories as $category)
                    <a href="{{ route('shop.home.category', $category->id) }}" class="text-decoration-none text-center d-block px-2">
                        <div class="category-circle-item mx-auto mb-2 rounded-circle shadow-sm {{ !$category->logo_url ? 'bg-white d-flex align-items-center justify-content-center' : '' }}"
                             style="width: 100px; height: 100px;
                                    @if($category->logo_url)
                                        background-image: url('{{ $category->logo_url }}');
                                        background-size: cover;
                                        background-position: center;
                                    @endif">

                            {{-- Fallback icon if no background logo --}}
                            @if(!$category->logo_url)
                                <i class="fas fa-shopping-bag text-primary fa-2x"></i>
                            @endif
                        </div>
                        <span class="text-dark fw-bold small">{{ $category->name }}</span>
                    </a>
                @endforeach
            @endisset
        </div>
    </div>
</div>

{{-- Services Section --}}
<div class="container-fluid px-lg-5 mb-5">
    <div class="row g-0 bg-white border rounded shadow-sm">
        @php
            $services = [
                ['icon' => 'fa-sync-alt', 'title' => 'Free Return', 'desc' => '30 days guarantee'],
                ['icon' => 'fab fa-telegram-plane', 'title' => 'Free Shipping', 'desc' => 'On all orders'],
                ['icon' => 'fas fa-life-ring', 'title' => 'Support 24/7', 'desc' => 'Online 24 hrs'],
                ['icon' => 'fas fa-credit-card', 'title' => 'Gift Cards', 'desc' => 'Receive rewards'],
                ['icon' => 'fas fa-lock', 'title' => 'Secure Pay', 'desc' => 'Safe transactions'],
                ['icon' => 'fas fa-blog', 'title' => 'Service', 'desc' => 'Quality support']
            ];
        @endphp
        @foreach($services as $index => $service)
            <div class="col-6 col-md-4 col-lg-2 border-end py-3 px-2 text-center">
                <i class="{{ $service['icon'] }} text-primary mb-2"></i>
                <h6 class="text-uppercase mb-1 fw-bold" style="font-size: 10px;">{{ $service['title'] }}</h6>
                <p class="mb-0 text-muted" style="font-size: 9px;">{{ $service['desc'] }}</p>
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
                            @foreach($collection->take(12) as $product)
                                <div class="col-6 col-md-4 col-lg-2">
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

{{-- Dynamic Category Sections --}}
@isset($homeCategories)
    @foreach($homeCategories as $category)
        @php
            $categoryProducts = app('Webkul\Product\Repositories\ProductRepository')->getAll([
                'category_id' => $category->id,
            ])->take(6);
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

                    <div class="row g-3">
                        @foreach($categoryProducts as $product)
                            <div class="col-6 col-md-4 col-lg-2">
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

<script>
    $(document).ready(function(){
        $(".category-carousel").owlCarousel({
            autoplay: true,
            smartSpeed: 1000,
            margin: 15,
            dots: false,
            loop: true,
            nav : true,
            navText : [
                '<i class="bi bi-arrow-left"></i>',
                '<i class="bi bi-arrow-right"></i>'
            ],
            responsive: {
                0:{ items:3 },
                576:{ items:4 },
                768:{ items:6 },
                992:{ items:8 }
            }
        });
    });
</script>

<style>
    /* Category Slider Specific */
    .category-circle-item {
        transition: transform 0.3s ease;
        border: 2px solid white;
    }
    .category-circle-item:hover {
        transform: scale(1.05);
    }
    .category-slider-wrapper .owl-nav {
        position: absolute;
        top: 50%;
        width: 100%;
        display: flex;
        justify-content: space-between;
        transform: translateY(-50%);
        pointer-events: none;
    }
    .category-slider-wrapper .owl-nav button {
        pointer-events: all;
        background: rgba(255,255,255,0.8) !important;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    /* Existing UI Styles */
    .category-sidebar .list-group-item { transition: all 0.2s ease; background: transparent; }
    .category-sidebar .category-item { position: relative; }
    .category-sidebar .category-item:hover > .list-group-item {
        color: #ff6600;
        background: #f8f9fa;
        padding-left: 1rem;
    }
    .category-submenu {
        position: absolute; top: 0; left: 100%; width: 350px; min-height: 100%;
        background: white; z-index: 1050; display: none; border-left: 1px solid #eee;
    }
    .category-item:hover .category-submenu { display: block; }
    .hover-orange:hover { color: #ff6600 !important; }
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
    .header-carousel.owl-carousel .owl-stage-outer { overflow: hidden !important; width: 100% !important; }
</style>


