<div class="container-fluid my-5 px-0">
    <div class="position-relative overflow-hidden rounded-3 shadow-sm category-promo-banner" style="background-image: url('{{ $bannerImage ?? 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?q=80&w=1600' }}');">

        <div class="banner-overlay"></div>

        <div class="position-relative h-100 container py-5 px-4 px-md-5 d-flex align-items-center">
            <div class="col-10 col-sm-8 col-md-6 col-lg-5 text-white my-3 banner-content">
                @if(isset($bannerTag))
                    <span class="badge bg-primary rounded-pill px-3 py-2 mb-3 text-uppercase fw-bold tracking-wider small">
                        {{ $bannerTag }}
                    </span>
                @endif

                <h2 class="display-6 fw-bold mb-3 lh-sm banner-title">
                    {!! $bannerTitle ?? 'Shoot your <span class="text-primary">story</span>, your way' !!}
                </h2>

                @if(isset($bannerDesc))
                    <p class="lead text-white-50 fs-6 mb-4 d-none d-sm-block">
                        {{ $bannerDesc }}
                    </p>
                @endif

                <a href="{{ $bannerUrl ?? '#' }}" class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold shadow-sm banner-btn">
                    {{ $bannerBtnText ?? 'Shop Now' }} <i class="fas fa-arrow-right ms-2 small"></i>
                </a>
            </div>
        </div>
    </div>
</div>
