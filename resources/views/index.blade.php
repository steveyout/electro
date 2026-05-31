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

        $productUrl = route('shop.home.product', $product->url_key);

        return '
        <div class="product-item rounded border bg-white h-100 mx-1">
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

    $promoBanners = [
        [
            'image' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?q=80&w=1600',
            'tag' => 'Featured Collection',
            'title' => 'Shoot your <span class="text-primary">story</span>, your way',
            'desc' => 'Discover our newest line of high-end camera bodies, professional setups, and premium glass.',
            'btn_text' => 'Shop Cameras',
            'url' => route('shop.home.category', 'digital-cameras-and-lenses')
        ],
        [
            'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?q=80&w=1600',
            'tag' => 'Premium Mobile & Tech',
            'title' => 'Power your <span class="text-primary">productivity</span> anywhere',
            'desc' => 'Upgrade to flagship performance with premium next-generation smartphones and wireless accessories.',
            'btn_text' => 'Explore Phones',
            'url' => route('shop.home.category', 'apple-products')
        ],
        [
            'image' => 'https://images.unsplash.com/photo-1593305841991-05c297ba4575?q=80&w=1600',
            'tag' => 'Hot Deals',
            'title' => 'Upgrade your <span class="text-primary">entertainment</span> hub',
            'desc' => 'Transform your living space with immersive, crystal clear LED Smart displays and audio set-ups.',
            'btn_text' => 'View Displays',
            'url' => route('shop.home.category', 'televisions')
        ]
    ];

    shuffle($promoBanners);
    $bannerIndex = 0;
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
                                <a href="{{ route('shop.home.category', $category->slug) }}"
                                   class="list-group-item list-group-item-action border-0 py-2 d-flex justify-content-between align-items-center">
                                    <span class="small">
                                        <i class="fas fa-list-ul me-2 text-muted" style="width: 15px;"></i>
                                        {{ $category->name }}
                                    </span>
                                    <i class="fas fa-chevron-right text-muted" style="font-size: 10px;"></i>
                                </a>

                                @if($category->children->count() > 0)
                                    <div class="category-submenu shadow-lg rounded-end">
                                        <div class="p-4">
                                            <h6 class="text-primary border-bottom pb-2 fw-bold">{{ $category->name }}</h6>
                                            <div class="row">
                                                @foreach($category->children as $child)
                                                    <div class="col-6 mb-2">
                                                        <a href="{{ route('shop.home.category', $child->slug) }}" class="text-decoration-none text-dark small hover-orange">
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
            @php
                // Prioritize mapped promotions, fall back to featured products
                $carouselItems = (isset($promotions) && $promotions->isNotEmpty()) ? $promotions : $featuredProducts;
            @endphp

            @if($carouselItems->count() > 0)
                <div class="shadow-sm rounded overflow-hidden h-100 position-relative" style="background: #F4F6F7;">
                    <div class="header-carousel owl-carousel py-0">
                        @foreach($carouselItems as $item)
                            @php
                                // Check if it's our mapped promotion object
                                $isPromo = property_exists($item, 'target_type');

                                $name = $isPromo ? $item->title : $item->name;
                                $image = $isPromo ? $item->image_url : $item->base_image_url;
                                $desc = $isPromo ? $item->description : \Illuminate\Support\Str::limit(strip_tags($item->short_description ?? ''), 100);

                                // Dynamic Link Logic using your confirmed route names
                                if ($isPromo) {
                                    $link = ($item->target_type === 'product')
                                        ? route('shop.home.product', $item->target_slug)
                                        :route('shop.home.category', $item->target_slug);

                                } else {
                                    // Fallback for non-promotional items
                                    $link = route('shop.home.product', $item->url_key);
                                }
                            @endphp

                            <div class="header-carousel-item" style="width: 100%;">
                                <div class="row g-0 align-items-center">
                                    <div class="col-md-7 carousel-content text-start p-4 p-lg-5">
                                        @if(!$isPromo && method_exists($item, 'getTypeInstance'))
                                            <h5 class="text-muted fw-light mb-2" style="font-size: 0.9rem;">
                                                Save Up To <span class="text-primary fw-bold">{{ core()->currency($item->price - $item->getTypeInstance()->getMinimalPrice()) }}</span>
                                            </h5>
                                        @endif
                                        <h2 class="fw-bold text-dark mb-3">{{ $name }}</h2>
                                        <div class="text-secondary mb-4 d-none d-md-block" style="font-size: 0.85rem;">{!! $desc !!}</div>
                                        <a class="btn btn-primary rounded-pill py-2 px-4 fw-bold" href="{{ $link }}">Shop Now</a>
                                    </div>
                                    <div class="col-md-5 text-center p-3">
                                        <img src="{{ $image }}" class="img-fluid hero-img" style="height: 320px; object-fit: contain; width: 100%;" alt="{{ $name }}">
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

{{-- Category Circle Slider Section --}}
<div class="container-fluid px-lg-5 mb-5">
    <div class="category-slider-wrapper p-3 rounded shadow-sm" style="background: #73C2D9;">
        <div class="category-carousel owl-carousel">
            @isset($homeCategories)
                @foreach($homeCategories as $category)
                    <a href="{{ route('shop.home.category', $category->slug) }}" class="text-decoration-none text-center d-block px-2">
                        <div class="category-circle-item mx-auto mb-2 rounded-circle shadow-sm {{ !$category->logo_url ? 'bg-white d-flex align-items-center justify-content-center' : '' }}"
                             style="width: 100px; height: 100px;
                                    @if($category->logo_url)
                                        background-image: url('{{ $category->logo_url }}');
                                        background-size: cover;
                                        background-position: center;
                                    @endif">
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
                        {{-- UPDATED: Added class 'category-products-carousel' and unique ID for slider initialization --}}
                        <div id="slider-{{ $id }}" class="category-products-carousel owl-carousel owl-theme">
                            @foreach($collection->take(12) as $product)
                                {!! $renderProductCard($product) !!}
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Dynamic Promo Banner (Top Banner) --}}
@if(isset($promoBanners[$bannerIndex]))
    @php $topBanner = $promoBanners[$bannerIndex]; @endphp
    <div class="container-fluid px-lg-5 my-4">
        <div class="position-relative overflow-hidden rounded-3 shadow-sm custom-category-banner"
             style="background-image: url('{{ $topBanner['image'] }}');">
            <div class="banner-gradient-overlay"></div>
            <div class="position-relative h-100 p-4 p-md-5 d-flex align-items-center dynamic-banner-content">
                <div class="col-11 col-sm-9 col-md-7 col-lg-5 text-white my-2">
                    <span class="badge bg-primary rounded-pill px-3 py-2 mb-3 text-uppercase fw-bold tracking-wider small">
                        {{ $topBanner['tag'] }}
                    </span>
                    <h2 class="display-6 fw-bold mb-3 lh-sm text-white">
                        {!! $topBanner['title'] !!}
                    </h2>
                    <p class="lead text-white-50 fs-6 mb-4 d-none d-sm-block">
                        {{ $topBanner['desc'] }}
                    </p>
                    <a href="{{ $topBanner['url'] }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm text-uppercase">
                        {{ $topBanner['btn_text'] }} <i class="fas fa-arrow-right ms-2 small"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @php $bannerIndex++; @endphp
@endif

<div id="all-categories-start"></div>

{{-- Dynamic Category Loops --}}
@isset($homeCategories)
    @php $validCategoryCount = 0; @endphp
    @foreach($homeCategories as $category)
        @php
            $categoryProducts = app('Webkul\Product\Repositories\ProductRepository')->getAll([
                'category_id' => $category->id,
            ])->take(12);
            $loopId = "cat-slider-" . $category->id;
        @endphp

        @if($categoryProducts->count() > 0)
            @php $validCategoryCount++; @endphp

            <div class="category-container-block my-4 {{ $validCategoryCount % 2 == 0 ? 'bg-wrap-light' : '' }}">
                <div class="category-header-strip py-3 px-3 px-lg-5 mb-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="category-strip-dot"></span>
                            <h4 class="m-0 text-white text-uppercase fw-bold tracking-wider" style="font-size: 1.05rem; letter-spacing: 0.5px;">
                                {{ $category->name }}
                            </h4>
                        </div>

                        {{-- Controls Container --}}
                        <div class="d-flex align-items-center gap-3 custom-nav-wrapper">
                            <a href="{{ route('shop.home.category', $category->slug) }}" class="category-strip-link text-white text-decoration-none small fw-bold d-flex align-items-center">
                                See All <i class="fas fa-chevron-right ms-2" style="font-size: 0.75rem;"></i>
                            </a>
                            <div class="strip-carousel-controls d-none d-md-flex gap-1">
                                <button class="btn-strip-nav prev-trigger" data-target="#{{ $loopId }}">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button class="btn-strip-nav next-trigger" data-target="#{{ $loopId }}">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Product Slider Track Node --}}
                <div class="container-fluid px-lg-5 pb-4 position-relative">
                    <div id="{{ $loopId }}" class="category-products-carousel owl-carousel owl-theme">
                        @foreach($categoryProducts as $product)
                            {!! $renderProductCard($product) !!}
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- In-between section promo banners mapping --}}
            @if($validCategoryCount % 2 == 0 && isset($promoBanners[$bannerIndex]))
                @php $banner = $promoBanners[$bannerIndex]; @endphp
                <div class="container-fluid px-lg-5 my-5">
                    <div class="position-relative overflow-hidden rounded-3 shadow-sm custom-category-banner"
                         style="background-image: url('{{ $banner['image'] }}');">
                        <div class="banner-gradient-overlay"></div>
                        <div class="position-relative h-100 p-4 p-md-5 d-flex align-items-center dynamic-banner-content">
                            <div class="col-11 col-sm-9 col-md-7 col-lg-5 text-white my-2">
                                <span class="badge bg-primary rounded-pill px-3 py-2 mb-3 text-uppercase fw-bold tracking-wider small">
                                    {{ $banner['tag'] }}
                                </span>
                                <h2 class="display-6 fw-bold mb-3 lh-sm text-white">
                                    {!! $banner['title'] !!}
                                </h2>
                                <p class="lead text-white-50 fs-6 mb-4 d-none d-sm-block">
                                    {{ $banner['desc'] }}
                                </p>
                                <a href="{{ $banner['url'] }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm text-uppercase">
                                    {{ $banner['btn_text'] }} <i class="fas fa-arrow-right ms-2 small"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @php $bannerIndex++; @endphp
            @endif
        @endif
    @endforeach
@endisset

@include('partials.footer')

<script>
    $(document).ready(function(){
        // Main Hero Carousel
        $(".header-carousel").owlCarousel({
            items: 1,
            autoplay: true,
            smartSpeed: 1500,
            loop: true,
            dots: true,
            nav : false,
            mouseDrag: true,
            touchDrag: true
        });

        // Top Categories Carousel
        $(".category-carousel").owlCarousel({
            autoplay: true,
            smartSpeed: 1000,
            margin: 15,
            dots: false,
            loop: true,
            nav : true,
            navText : ['<i class="bi bi-arrow-left"></i>', '<i class="bi bi-arrow-right"></i>'],
            responsive: {
                0:{ items:3 },
                576:{ items:4 },
                768:{ items:6 },
                992:{ items:8 }
            }
        });

        // Product Track Initialization
        $('.category-products-carousel').each(function() {
            $(this).owlCarousel({
                autoplay: true,
                autoplayTimeout: 4000,
                autoplayHoverPause: true,
                smartSpeed: 1200,
                margin: 15,
                dots: false,
                loop: true,
                nav: false,
                mouseDrag: true,
                touchDrag: true,
                responsive: {
                    0: { items: 2 },
                    576: { items: 3 },
                    768: { items: 4 },
                    992: { items: 5 },
                    1200: { items: 6 }
                }
            });
        });

        // Custom External Header Navigation Directives
        $(document).on('click', '.prev-trigger', function() {
            var target = $(this).data('target');
            $(target).trigger('prev.owl.carousel', [1200]);
        });

        $(document).on('click', '.next-trigger', function() {
            var target = $(this).data('target');
            $(target).trigger('next.owl.carousel', [1200]);
        });

        // Add this inside your $(document).ready function
        $('a[data-bs-toggle="pill"]').on('shown.bs.tab', function (e) {
            $('.category-products-carousel').trigger('refresh.owl.carousel');
        });


    });
</script>

<style>
    /* Full-Width Category Block Grid */
    .category-container-block {
        width: 100%;
        position: relative;
    }
    .bg-wrap-light {
        background-color: #f8f9fa;
    }
    .category-header-strip {
        background: #ff6600;
        background: linear-gradient(90deg, #ff6600 0%, #ff8533 100%);
        border-bottom: 2px solid rgba(0,0,0,0.05);
    }
    .category-container-block:nth-of-type(even) .category-header-strip {
        background: #73C2D9;
        background: linear-gradient(90deg, #53b3ce 0%, #73C2D9 100%);
    }
    .category-strip-dot {
        width: 7px;
        height: 7px;
        background-color: #ffffff;
        border-radius: 50%;
        display: inline-block;
        opacity: 0.85;
    }
    .category-strip-link {
        transition: transform 0.2s ease, opacity 0.2s ease;
    }
    .category-strip-link:hover {
        opacity: 0.9;
        transform: translateX(-2px);
    }

    /* Fixed Header Navigation Buttons Layout */
    .custom-nav-wrapper {
        display: flex;
        align-items: center;
    }
    .strip-carousel-controls {
        display: flex;
        align-items: center;
        border-left: 1px solid rgba(255, 255, 255, 0.3);
        padding-left: 12px;
    }
    .btn-strip-nav {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.25);
        color: #ffffff;
        width: 28px;
        height: 28px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-strip-nav:hover {
        background: #ffffff;
        color: #ff6600;
        border-color: #ffffff;
    }
    .category-container-block:nth-of-type(even) .btn-strip-nav:hover {
        color: #53b3ce;
    }
    .btn-strip-nav:active {
        transform: scale(0.95);
    }

    .category-products-carousel .owl-item {
        display: flex;
        justify-content: center;
    }
    .category-products-carousel .product-item {
        width: 100%;
        min-width: 0;
    }

    /* Custom Category Banner Formatting */
    .custom-category-banner {
        background-size: cover;
        background-position: center right;
        background-repeat: no-repeat;
        min-height: 320px;
        height: auto;
    }
    .banner-gradient-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(90deg, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.4) 60%, rgba(0,0,0,0) 100%);
        z-index: 1;
    }
    .dynamic-banner-content {
        z-index: 2;
    }
    .tracking-wider {
        letter-spacing: 1px;
    }
    @media (max-width: 767.98px) {
        .custom-category-banner {
            background-position: center;
            min-height: 260px;
        }
        .banner-gradient-overlay {
            background: rgba(0, 0, 0, 0.65);
        }
    }

    /* Styling for the Dots on the Header Carousel */
    .header-carousel .owl-dots {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 8px;
        z-index: 10;
    }
    .header-carousel .owl-dot span {
        width: 12px;
        height: 12px;
        background: rgba(0, 0, 0, 0.2) !important;
        display: block;
        border-radius: 50%;
        transition: all 0.3s ease;
    }
    .header-carousel .owl-dot.active span {
        background: #ff6600 !important;
        width: 25px;
        border-radius: 10px;
    }

    /* Category Slider Specific */
    .category-circle-item { transition: transform 0.3s ease; border: 2px solid white; }
    .category-circle-item:hover { transform: scale(1.05); }

    /* General UI Components */
    .category-sidebar .list-group-item { transition: all 0.2s ease; background: transparent; }
    .category-sidebar .category-item:hover > .list-group-item { color: #ff6600; background: #f8f9fa; padding-left: 1rem; }
    .category-submenu { position: absolute; top: 0; left: 100%; width: 350px; min-height: 100%; background: white; z-index: 1050; display: none; border-left: 1px solid #eee; }
    .category-item:hover .category-submenu { display: block; }
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
</style>


