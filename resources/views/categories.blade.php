@include('partials.header')
<!-- Single Page Header start -->
<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6 wow fadeInUp" data-wow-delay="0.1s">Product categories</h1>
    <ol class="breadcrumb justify-content-center mb-0 wow fadeInUp" data-wow-delay="0.3s">
        <li class="breadcrumb-item"><a href="{{route('shop.home.index')}}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{route('shop.home.categories')}}">Categories</a></li>
    </ol>
</div>
<!-- Single Page Header End -->

<!-- Products Categories -->
<div class="container-fluid shop py-5">
    <div class="container py-5">
        <div class="row g-4 fade show">
            @if(count($categories['data'])>0)
                @foreach($categories['data'] as $category)
                    <div class="col-lg-6 wow fadeInLeft" data-wow-delay="0.1s">
                        <a href="{{config('app.url')}}/category/{{$category['id']}}">
                            <div class="bg-primary rounded position-relative">
                                <img src="{{$category['banner_url']}}" class="img-fluid w-100 rounded" alt="">
                                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center rounded p-4"
                                     style="background: rgba(255, 255, 255, 0.5);">
                                    <h3 class="display-5 text-primary">{{$category['name']}}</h3>
                                    <a href="{{config('app.url')}}/category/{{$category['id']}}" class="btn btn-primary rounded-pill align-self-start py-2 px-4">Shop Now</a>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
<!-- Products Categories -->

@include('partials.footer')
