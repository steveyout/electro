@include('partials.header')

@php
    /**
     * Helper function for rendering product cards to match the Home Page.
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

<div class="container-fluid page-header py-5 mb-5" style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('{{ asset('themes/shop/electro/images/banner-1.jpg') }}'); background-size: cover;">
    <div class="container text-center py-5">
        <h1 class="text-white display-4 mb-3">Shop Products</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="{{ route('shop.home.index') }}" class="text-white">Home</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">Products</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container-fluid py-5">
    <div class="container-fluid px-lg-5">
        <div class="row g-4">
            <div class="col-lg-3">
                <form id="filter-form" action="{{ route('shop.search.index') }}" method="GET" class="bg-light p-4 rounded shadow-sm">
                    <input type="hidden" name="term" value="{{ request('term') }}">

                    <div class="mb-4">
                        <h4 class="mb-3 border-bottom pb-2">Categories</h4>
                        @foreach($categories as $category)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="category" value="{{ $category->id }}"
                                       id="cat-{{ $category->id }}" {{ request('category') == $category->id ? 'checked' : '' }}
                                       onchange="this.form.submit()">
                                <label class="form-check-label text-dark" for="cat-{{ $category->id }}">
                                    {{ $category->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <div class="mb-4">
                        <h4 class="mb-3 border-bottom pb-2">Price Range</h4>
                        <input type="range" class="form-range" name="price_max" min="0" max="100000" step="1000"
                               value="{{ request('price_max') ?? 100000 }}" oninput="priceOutput.value = this.value" onchange="this.form.submit()">
                        <div class="d-flex justify-content-between">
                            <span class="small">Min: 0</span>
                            <span class="text-primary fw-bold">Ksh <output id="priceOutput">{{ request('price_max') ?? 100000 }}</output></span>
                        </div>
                    </div>

                    <a href="{{ route('shop.home.products') }}" class="btn btn-outline-danger btn-sm w-100">Clear All Filters</a>
                </form>

                <div class="mt-5 d-none d-lg-block">
                    <h4 class="mb-4 border-bottom pb-2">Featured Products</h4>
                    @foreach($featuredProducts->take(3) as $fProduct)
                        <div class="d-flex align-items-center mb-3 bg-white p-2 rounded border">
                            <img src="{{ $fProduct->base_image_url }}" class="img-fluid rounded" style="width: 60px; height: 60px; object-fit: contain;">
                            <div class="ms-3">
                                <h6 class="mb-0 text-truncate" style="max-width: 150px;">
                                    <a href="{{ route('shop.home.product', $fProduct->url_key) }}" class="text-dark text-decoration-none">{{ $fProduct->name }}</a>
                                </h6>
                                <span class="text-primary fw-bold" style="font-size: 0.9rem;">{{ core()->currency($fProduct->getTypeInstance()->getMinimalPrice()) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-lg-9">
                <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                    <p class="mb-0 text-muted">
                        Showing {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} items
                    </p>
                    <div class="d-flex align-items-center">
                        <span class="me-2 text-nowrap d-none d-md-inline">Sort By:</span>
                        <select class="form-select form-select-sm border-0 bg-light" style="width: 150px;">
                            <option>Default</option>
                            <option>Price: Low to High</option>
                            <option>Price: High to Low</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3">
                    @forelse($products as $product)
                        <div class="col-6 col-md-4 col-lg-3">
                            {!! $renderProductCard($product) !!}
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <div class="mb-3"><i class="fa fa-search fa-4x text-light-gray"></i></div>
                            <h4 class="text-muted">No products found for "{{ request('term') }}"</h4>
                            <p>Try adjusting your filters or search keywords.</p>
                        </div>
                    @endforelse
                </div>

                <div class="d-flex justify-content-center mt-5">
                    {{ $products->appends(request()->input())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials.footer')

<style>
    /* Card Specific Styles to match Index exactly */
    .product-item img { transition: transform 0.5s ease; }
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

    .text-light-gray { color: #dee2e6; }
</style>
