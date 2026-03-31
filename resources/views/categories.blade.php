@include('partials.header')

<style>
    .category-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        height: 350px;
        background-color: #f8f9fa;
        border: 1px solid #eee;
    }
    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0,0,0,0.1) !important;
        border-color: var(--bs-primary);
    }
    .category-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }
    .category-card:hover .category-image {
        transform: scale(1.1);
    }
    .category-overlay {
        background: linear-gradient(to right, rgba(255,255,255,0.95) 20%, rgba(255,255,255,0.1) 100%);
        z-index: 2;
    }
    .category-title {
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .category-card .btn-primary {
        transition: all 0.3s ease;
    }
    .category-card:hover .btn-primary {
        padding-left: 2rem;
        padding-right: 2rem;
    }
</style>

<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6">Product Categories</h1>
    <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item"><a href="{{ route('shop.home.index') }}" class="text-white">Home</a></li>
        <li class="breadcrumb-item active text-white">Categories</li>
    </ol>
</div>

<div class="container-fluid shop py-5">
    <div class="container py-5">
        <div class="row g-4">
            @if($categories->count() > 0)
                @foreach($categories as $category)
                    @php
                        // Fetch the correct image URL from the Bagisto category object
                        $image = $category->image_url ?? $category->banner_url ?? asset('vendor/webkul/ui/assets/images/product-placeholder.webp');

                        // Updated to use your new route name
                        $categoryUrl = route('shop.home.category', $category->id);
                    @endphp

                    <div class="col-lg-6">
                        <div class="category-card rounded position-relative shadow-sm">
                            <img src="{{ $image }}" class="category-image" alt="{{ $category->name }}">

                            <div class="category-overlay position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center p-5">
                                <h3 class="display-6 text-primary category-title mb-2">{{ $category->name }}</h3>

                                @if($category->description)
                                    <p class="text-dark d-none d-md-block mb-4" style="max-width: 75%; line-height: 1.6;">
                                        {{ Str::limit(strip_tags($category->description), 120) }}
                                    </p>
                                @endif

                                <div>
                                    <a href="{{ $categoryUrl }}" class="btn btn-primary rounded-pill py-2 px-4 shadow-sm fw-bold">
                                        View Collection <i class="fas fa-arrow-right ms-2 small"></i>
                                    </a>
                                </div>
                            </div>

                            <a href="{{ $categoryUrl }}" class="stretched-link"></a>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-12 text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-layer-group fa-4x text-light"></i>
                    </div>
                    <h4 class="text-muted">No categories available right now.</h4>
                    <p class="text-muted mb-4">Check back later or browse our featured products.</p>
                    <a href="{{ route('shop.home.index') }}" class="btn btn-primary px-5 py-3 rounded-pill">Return to Home</a>
                </div>
            @endif
        </div>
    </div>
</div>

@include('partials.footer')
