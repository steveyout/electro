<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ config('app.name') }}</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <link href="{{ asset('themes/shop/electro/lib/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('themes/shop/electro/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">

    <link href="{{ asset('themes/shop/electro/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-star-rating@4.1.2/css/star-rating.min.css" media="all" rel="stylesheet" type="text/css" />

    <link href="{{ asset('themes/shop/electro/css/style.css') }}?v={{ filemtime(public_path('themes/shop/electro/css/style.css')) }}" rel="stylesheet">

    <style>
        /* WhatsApp Floating Button */
        .whatsapp-float-left {
            position: fixed; width: 60px; height: 60px; bottom: 40px; left: 40px;
            background-color: #25d366; color: #FFF; border-radius: 50px;
            text-align: center; font-size: 30px; box-shadow: 2px 2px 3px rgba(0,0,0,0.2);
            z-index: 1000; display: flex; align-items: center; justify-content: center;
            text-decoration: none; transition: all 0.3s ease;
        }

        /* Category Hover Fixes */
        .categories-bars-item { transition: all 0.3s ease; background: transparent !important; }
        .categories-bars-item a { display: block; padding: 10px 20px; color: #333 !important; text-decoration: none; }
        .categories-bars-item a:hover { color: var(--bs-primary) !important; padding-left: 30px !important; background: transparent !important; }

        /* Responsive Mobile UI */
        @media (max-width: 991.98px) {
            .mobile-search-container { padding: 10px 15px; background: #f8f9fa; border-bottom: 1px solid #eee; }
            .nav-bar .bg-primary { padding-left: 15px !important; padding-right: 15px !important; }
            #allCat { position: relative !important; width: 100% !important; box-shadow: none !important; border: 1px solid #eee; }
            .navbar-brand img { max-height: 30px; }
            .cart-badge-mobile { font-size: 0.65rem; padding: 0.25em 0.5em; }
        }

        .dropdown-item.active, .dropdown-item:active { background-color: var(--bs-primary); }

        /* Mini Cart Styling */
        .offcanvas-footer { position: sticky; bottom: 0; background: #fff; z-index: 10; }
        .cart-item-row img { transition: transform 0.3s ease; }
        .cart-item-row:hover img { transform: scale(1.05); }
    </style>

    <script>
        window.AppConfig = {
            baseUrl: "{{ url('/') }}",
            csrfToken: "{{ csrf_token() }}",
            cartAddUrl: "{{ route('shop.cart.add', '') }}"
        };
    </script>
</head>

<body>

@php
    $cart = Webkul\Checkout\Facades\Cart::getCart();
    $categoryData = isset($categories['data']) ? $categories['data'] : $categories;
@endphp

<div class="container-fluid px-5 d-none d-lg-block border-bottom">
    <div class="row gx-0 align-items-center" style="height: 45px;">
        <div class="col-lg-6 text-start">
            <a href="#" class="text-muted small me-3">Help</a>
            <a href="{{ route('shop.home.contact') }}" class="text-muted small">Contact</a>
        </div>
        <div class="col-lg-6 text-end">
            <div class="d-inline-flex align-items-center">
                @guest('customer')
                    <i class="fa fa-user text-primary me-2"></i>
                    <a href="{{ route('customer.session.index') }}" class="text-muted small me-2">Login</a>
                    <span class="text-muted small">|</span>
                    <a href="{{ route('customer.register.index') }}" class="text-muted small ms-2">Register</a>
                @else
                    <div class="dropdown">
                        <a href="javascript:void(0);" class="text-muted small dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fa fa-user text-primary me-2"></i>Hi, {{ auth()->guard('customer')->user()->first_name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li><a class="dropdown-item small" href="{{ route('customer.profile.index') }}">My Profile</a></li>
                            <li><a class="dropdown-item small" href="{{ route('customer.orders.index') }}">Orders</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('customer.session.destroy') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item small text-danger border-0 bg-transparent">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endguest
            </div>
        </div>
    </div>
</div>

<div class="container-fluid d-lg-none bg-white py-2 border-bottom sticky-top">
    <div class="d-flex justify-content-between align-items-center px-2">
        <a href="{{ route('shop.home.index') }}">
            <img src="{{ asset('themes/shop/electro/images/logo.png') }}" alt="Logo" style="height: 30px;">
        </a>
        <div class="d-flex align-items-center">
            @guest('customer')
                <a href="{{ route('customer.session.index') }}" class="text-dark me-3">
                    <i class="fas fa-user fs-5"></i>
                </a>
            @else
                <a href="{{ route('customer.profile.index') }}" class="text-primary me-3">
                    <i class="fas fa-user-check fs-5"></i>
                </a>
            @endguest
            <button class="btn position-relative p-0 me-1" data-bs-toggle="offcanvas" data-bs-target="#miniCartDrawer">
                <i class="fas fa-shopping-cart fs-5 text-dark"></i>
                <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle cart-badge-mobile cart-count">
                    {{ $cart ? $cart->items_count : 0 }}
                </span>
            </button>
        </div>
    </div>
</div>

<div class="container-fluid d-lg-none mobile-search-container">
    <form action="{{ route('shop.search.index') }}" method="GET">
        <div class="input-group">
            <input type="text" name="term" class="form-control border-end-0 rounded-start-pill ps-3" placeholder="Search for products...">
            <button class="btn btn-primary rounded-end-pill px-3" type="submit"><i class="fas fa-search"></i></button>
        </div>
    </form>
</div>

<div class="container-fluid px-5 py-4 d-none d-lg-block">
    <div class="row align-items-center">
        <div class="col-lg-3">
            <a href="{{ route('shop.home.index') }}" class="navbar-brand p-0">
                <img src="{{ asset('themes/shop/electro/images/logo.png') }}" alt="Logo">
            </a>
        </div>
        <div class="col-lg-6">
            <form action="{{ route('shop.search.index') }}" method="GET">
                <div class="d-flex border rounded-pill overflow-hidden">
                    <input class="form-control border-0 px-4 py-3" name="term" type="text" value="{{ request('term') }}" placeholder="Search products...">
                    <select class="form-select border-0 border-start rounded-0" name="category" style="width: 180px;">
                        <option value="">All Categories</option>
                        @isset($categoryData)
                            @foreach($categoryData as $category)
                                <option value="{{ is_array($category) ? $category['id'] : $category->id }}" {{ request('category') == (is_array($category) ? $category['id'] : $category->id) ? 'selected' : '' }}>
                                    {{ is_array($category) ? $category['name'] : $category->name }}
                                </option>
                            @endforeach
                        @endisset
                    </select>
                    <button type="submit" class="btn btn-primary px-4 border-0"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>
        <div class="col-lg-3 text-end">
            <button class="btn position-relative text-muted" data-bs-toggle="offcanvas" data-bs-target="#miniCartDrawer">
                <span class="rounded-circle btn-md-square border d-inline-flex align-items-center justify-content-center">
                    <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle cart-count">
                        {{ $cart ? $cart->items_count : 0 }}
                    </span>
                    <i class="fas fa-shopping-cart"></i>
                </span>
                <span class="text-dark ms-2 fw-bold cart-total-display">
                    {!! $cart ? core()->currency($cart->base_grand_total) : core()->currency(0) !!}
                </span>
            </button>
        </div>
    </div>
</div>

<div class="container-fluid nav-bar p-0">
    <div class="row gx-0 bg-primary px-lg-5 align-items-center">
        <div class="col-lg-3 d-none d-lg-block">
            <nav class="navbar navbar-light position-relative p-0">
                <button class="btn btn-primary w-100 text-start py-3 px-0 border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#allCat">
                    <h4 class="m-0 text-white"><i class="fa fa-bars me-2"></i>All Categories</h4>
                </button>
                <div class="collapse navbar-collapse rounded-bottom bg-white shadow" id="allCat" style="position: absolute; width: 100%; z-index: 999;">
                    <ul class="list-unstyled mb-0">
                        @if($categoryData && count($categoryData) > 0)
                            @foreach($categoryData as $category)
                                <li class="border-bottom">
                                    <div class="categories-bars-item">
                                        <a href="{{ url('categories/' . (is_array($category) ? ($category['url_path'] ?? $category['id']) : ($category->url_path ?? $category->id))) }}">
                                            {{ is_array($category) ? $category['name'] : $category->name }}
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        @else
                            <li class="p-3 text-muted small">No categories available</li>
                        @endif
                    </ul>
                </div>
            </nav>
        </div>

        <div class="col-lg-9">
            <nav class="navbar navbar-expand-lg navbar-dark bg-primary p-0">
                <button class="navbar-toggler ms-2 my-2 border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars text-white"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0">
                        <a href="{{ route('shop.home.index') }}" class="nav-item nav-link {{ request()->is('/') ? 'active' : '' }}">Home</a>
                        <a href="{{ route('shop.home.products') }}" class="nav-item nav-link {{ request()->is('products*') ? 'active' : '' }}">Products</a>
                        <a href="{{ route('shop.home.categories') }}" class="nav-item nav-link">Categories</a>
                        <a href="{{ route('shop.home.contact') }}" class="nav-item nav-link">Contact</a>

                        <div class="d-lg-none border-top mt-2 pt-2">
                            @guest('customer')
                                <a href="{{ route('customer.session.index') }}" class="nav-item nav-link py-2"><i class="fa fa-sign-in-alt me-2"></i>Login</a>
                                <a href="{{ route('customer.register.index') }}" class="nav-item nav-link py-2"><i class="fa fa-user-plus me-2"></i>Signup</a>
                            @else
                                <a href="{{ route('customer.profile.index') }}" class="nav-item nav-link py-2"><i class="fa fa-user me-2"></i>My Profile</a>
                                <form action="{{ route('customer.session.destroy') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="nav-item nav-link py-2 border-0 bg-transparent w-100 text-start text-white">
                                        <i class="fa fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                            @endguest
                        </div>
                    </div>
                    <a href="tel:+254721966663" class="btn btn-secondary rounded-pill py-2 px-4 d-none d-lg-block ms-3"><i class="fa fa-phone-alt me-2"></i>+254 721 966663</a>
                </div>
            </nav>
        </div>
    </div>
</div>

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


<div class="mobile-bottom-nav d-lg-none">
    <div class="nav-item">
        <a href="{{ route('shop.home.index') }}" class="{{ request()->is('/') ? 'active' : '' }}">
            <i class="fa fa-home"></i>
            <span>Home</span>
        </a>
    </div>

    <div class="nav-item">
        <a href="{{ route('shop.checkout.cart.index') }}" class="position-relative {{ request()->is('checkout/cart*') ? 'active' : '' }}">
            <i class="fa fa-shopping-cart"></i>
            {{-- Inline calculation to prevent "Undefined Variable" error --}}
            <span class="nav-cart-badge">
                {{ Webkul\Checkout\Facades\Cart::getCart() ? Webkul\Checkout\Facades\Cart::getCart()->items_count : 0 }}
            </span>
            <span>Cart</span>
        </a>
    </div>

    <div class="nav-item">
        <a href="{{ route('shop.checkout.onepage.index') }}">
            <i class="fa fa-credit-card"></i>
            <span>Checkout</span>
        </a>
    </div>

    <div class="nav-item">
        <a href="" class="{{ request()->is('customer/account*') ? 'active' : '' }}">
            <i class="fa fa-user"></i>
            <span>Account</span>
        </a>
    </div>
</div>


<a href="https://wa.me/254721966663" class="whatsapp-float-left" target="_blank">
    <i class="fab fa-whatsapp"></i>
</a>
