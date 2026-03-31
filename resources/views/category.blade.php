@include('partials.header')

@php
    /**
     * Helper function for rendering product cards.
     */
    $renderProductCard = function($product) {
        if (!$product) return '';

        $productInstance = $product->getTypeInstance();
        $minP = $productInstance->getMinimalPrice();
        $regP = $product->price;
        $disc = ($regP > 0 && $regP > $minP) ? round((($regP - $minP) / $regP) * 100) : 0;

        $productUrl = route('shop.home.product', $product->id);

        return '
        <div class="product-item rounded border bg-white h-100 shadow-sm">
            <div class="position-relative overflow-hidden p-3 text-center">
                ' . ($disc > 0 ? '<div class="discount-badge">' . $disc . '% <br> <span>off</span></div>' : '') . '
                <img src="' . $product->base_image_url . '" class="img-fluid" style="height:180px; width: auto; object-fit:contain;" alt="' . $product->name . '">
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
                <a class="h6 d-block text-truncate mb-2 text-decoration-none text-dark" href="' . $productUrl . '">' . $product->name . '</a>
                <div class="d-flex flex-column justify-content-center align-items-center">
                    <span class="text-primary fw-bold mb-0" style="font-size: 1.1rem;">' . core()->currency($minP) . '</span>
                    ' . ($minP < $regP ? '<span class="text-muted text-decoration-line-through small" style="margin-top: -2px;">' . core()->currency($regP) . '</span>' : '') . '
                </div>
            </div>
        </div>';
    };

    $banner = $category->banner_url ?? asset('themes/shop/electro/images/banner-1.jpg');
@endphp

<style>
    .product-item { transition: all 0.3s ease; }
    .product-item:hover { border-color: #ffb400 !important; transform: translateY(-3px); }
    .product-item img { transition: transform 0.5s ease; }
    .product-item:hover img { transform: scale(1.08); }
    .discount-badge {
        position: absolute; top: 10px; right: 10px; background: #ffb400; color: #000;
        font-weight: bold; padding: 4px 8px; border-radius: 2px; line-height: 1.1;
        text-align: center; z-index: 10; font-size: 13px;
    }
    .product-action {
        position: absolute; width: 100%; height: 100%; top: 0; left: 0;
        display: flex; align-items: center; justify-content: center;
        background: rgba(255, 255, 255, 0.6); opacity: 0; transition: 0.5s;
    }
    .product-item:hover .product-action { opacity: 1; }
    .btn-square { width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 4px; }
</style>

<div class="container-fluid page-header py-5 mb-5" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('{{ $banner }}'); background-size: cover; background-position: center;">
    <div class="container text-center py-5">
        <h1 class="text-white display-4 mb-3">{{ $category->name }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center mb-0">
                <li class="breadcrumb-item"><a href="{{ route('shop.home.index') }}" class="text-white">Home</a></li>
                <li class="breadcrumb-item active text-white">{{ $category->name }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container-fluid py-5">
    <div class="container-fluid px-lg-5">
        <div class="row g-4">
            <div class="col-lg-3">
                <div class="bg-light p-4 rounded shadow-sm border">
                    <h4 class="mb-3 border-bottom pb-2">Price Filter</h4>
                    <form action="{{ url()->current() }}" method="GET">
                        @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                        <input type="range" class="form-range" name="price_max" min="0" max="200000" step="1000"
                               value="{{ request('price_max') ?? 200000 }}"
                               oninput="priceOutput.value = this.value"
                               onchange="this.form.submit()">
                        <div class="d-flex justify-content-between mt-2">
                            <span class="text-primary fw-bold">Max: {{ core()->currency(request('price_max') ?? 200000) }}</span>
                        </div>
                        <output id="priceOutput" class="d-none">{{ request('price_max') ?? 200000 }}</output>
                    </form>

                    @if($category->children->count() > 0)
                        <h4 class="mb-3 mt-5 border-bottom pb-2">Sub Categories</h4>
                        <ul class="list-unstyled">
                            @foreach($category->children as $sub)
                                <li class="mb-2">
                                    <a href="{{ route('shop.home.category', $sub->id) }}" class="text-dark text-decoration-none">
                                        <i class="fas fa-chevron-right me-2 small text-primary"></i>{{ $sub->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="mt-5 d-none d-lg-block">
                    <h4 class="mb-4 border-bottom pb-2">Top Deals</h4>
                    @php
                        // FIX: Use the getAll logic that you confirmed works
                        $featured = app('Webkul\Product\Repositories\ProductRepository')->getAll(['featured' => 1])->take(3);
                    @endphp
                    @foreach($featured as $fProduct)
                        <div class="d-flex align-items-center mb-3 bg-white p-2 rounded border shadow-sm">
                            <img src="{{ $fProduct->base_image_url }}" class="img-fluid rounded" style="width: 50px; height: 50px; object-fit: contain;">
                            <div class="ms-3">
                                <h6 class="mb-0 text-truncate" style="max-width: 140px;">
                                    <a href="{{ route('shop.home.product', $fProduct->id) }}" class="text-dark text-decoration-none small fw-bold">{{ $fProduct->name }}</a>
                                </h6>
                                <span class="text-primary" style="font-size: 0.85rem;">{{ core()->currency($fProduct->getTypeInstance()->getMinimalPrice()) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-lg-9">
                <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                    <p class="mb-0 text-muted">Showing <strong>{{ $products->count() }}</strong> items</p>
                    <div class="d-flex align-items-center">
                        <select class="form-select form-select-sm border-0 bg-light" style="width: 160px;" onchange="window.location.href=this.value">
                            <option value="{{ url()->current() }}">Sort: Default</option>
                            <option value="{{ url()->current() }}?sort=price-low-high" {{ request('sort') == 'price-low-high' ? 'selected' : '' }}>Price: Low-High</option>
                            <option value="{{ url()->current() }}?sort=price-high-low" {{ request('sort') == 'price-high-low' ? 'selected' : '' }}>Price: High-Low</option>
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
                            <h4 class="text-muted">No products found.</h4>
                        </div>
                    @endforelse
                </div>

                <div class="d-flex justify-content-center mt-5">
                    {!! $products->appends(request()->input())->links('pagination::bootstrap-4') !!}
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials.footer')

