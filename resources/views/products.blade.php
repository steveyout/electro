@include('partials.header')
<!-- Single Page Header start -->
<div class="container-fluid page-header py-5">
    <h1 class="text-center text-white display-6 wow fadeInUp" data-wow-delay="0.1s">Products Page</h1>
    <ol class="breadcrumb justify-content-center mb-0 wow fadeInUp" data-wow-delay="0.3s">
        <li class="breadcrumb-item"><a href="{{route('shop.home.index')}}">Home</a></li>
        <li class="breadcrumb-item active text-white">Products</li>
    </ol>
</div>
<!-- Single Page Header End -->

<!-- Shop Page Start -->
<div class="container-fluid shop py-5">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-3 wow fadeInUp" data-wow-delay="0.1s">
                <div class="product-categories mb-4">
                    <h4>Products Categories</h4>
                    <ul class="list-unstyled">
                        @if(count($categories['data'])>0)
                            @foreach($categories['data'] as $category)
                                <li>
                                    <div class="categories-item">
                                        <a href="#" class="text-dark"><i class="fas fa-apple-alt text-secondary me-2"></i>
                                            {{$category['name']}}</a>
                                    </div>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
                <div class="price mb-4">
                    <h4 class="mb-2">Price</h4>
                    <input type="range" class="form-range w-100" id="rangeInput" name="rangeInput" min="0" max="500"
                           value="0" oninput="amount.value=rangeInput.value">
                    <output id="amount" name="amount" min-velue="0" max-value="500" for="rangeInput">0</output>
                    <div class=""></div>
                </div>

                <div class="featured-product mb-4">
                    <h4 class="mb-3">Featured products</h4>

                    @if(count($featuredProducts['data'])>0)
                        @foreach($featuredProducts['data'] as $featuredProduct)
                            <div class="featured-product-item">
                                <div class="rounded me-4" style="width: 100px; height: 100px;">
                                    <img src="{{$featuredProduct['images'][0]['original_image_url']}}" class="img-fluid rounded" alt="Image">
                                </div>
                                <div>
                                    <h6 class="mb-2">SmartPhone</h6>
                                    <div class="d-flex mb-2">
                                        <input  data-show-clear="false" type="text" class="rating" data-size="sm" value="{{$featuredProduct['ratings']['average']}}" disabled>
                                    </div>
                                    <div class="d-flex mb-2">
                                        @if($featuredProduct['on_sale'])
                                            <h5 class="fw-bold me-2">{{$featuredProduct['prices']['regular']['formatted_price']}}</h5>
                                        @endif
                                        <h5 class="text-danger text-decoration-line-through">{{$featuredProduct['prices']['final']['formatted_price']}}</h5>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif

            </div>
            </div>


            <div class="col-lg-9 wow fadeInUp" data-wow-delay="0.1s">

                <!--
                <div class="rounded mb-4 position-relative">
                    <img src="img/product-banner-3.jpg" class="img-fluid rounded w-100" style="height: 250px;"
                         alt="Image">
                    <div class="position-absolute rounded d-flex flex-column align-items-center justify-content-center text-center"
                         style="width: 100%; height: 250px; top: 0; left: 0; background: rgba(242, 139, 0, 0.3);">
                        <h4 class="display-5 text-primary">SALE</h4>
                        <h3 class="display-4 text-white mb-4">Get UP To 50% Off</h3>
                        <a href="#" class="btn btn-primary rounded-pill">Shop Now</a>
                    </div>
                </div>
                -->

                <div class="row g-4">
                    <div class="col-xl-8">
                        <div class="input-group w-100 mx-auto d-flex">
                            <input type="search" class="form-control p-3" placeholder="keywords"
                                   aria-describedby="search-icon-1">
                            <span id="search-icon-1" class="input-group-text p-3"><i
                                    class="fa fa-search"></i></span>
                        </div>
                    </div>
                    <div class="col-xl-4 text-end">
                        <div class="bg-light ps-3 py-3 rounded d-flex justify-content-between">
                            <label for="electronics">Sort By:</label>
                            <select id="electronics" name="electronicslist"
                                    class="border-0 form-select-sm bg-light me-3" form="electronicsform">
                                <option value="volvo">Default Sorting</option>
                                <option value="volv">Nothing</option>
                                <option value="sab">Popularity</option>
                                <option value="saab">Newness</option>
                                <option value="opel">Average Rating</option>
                                <option value="audio">Low to high</option>
                                <option value="audi">High to low</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!--begin products -->
                <div class="tab-content">
                    <div id="tab-5" class="tab-pane fade show p-0 active">
                        <div class="row g-4 product">

                            @if(count($products['data'])>0)
                                @foreach($products['data'] as $product)
                            <div class="col-lg-4">
                                <div class="product-item rounded wow fadeInUp" data-wow-delay="0.1s">
                                    <div class="product-item-inner border rounded">
                                        <div class="product-item-inner-item">
                                            <img src="{{$product['images'][0]['original_image_url']}}" class="img-fluid w-100 rounded-top" alt="">
                                            @if($product['is_new'])
                                                <div class="product-new">New</div>
                                            @endif
                                            @if($product['on_sale'])
                                                <div class="product-new">Offer</div>
                                            @endif
                                            <div class="product-details">
                                                <a href="{{config('app.url')}}/product/{{$product['id']}}"><i class="fa fa-eye fa-1x"></i></a>
                                            </div>
                                        </div>
                                        <div class="text-center rounded-bottom p-4">
                                            <a href="{{config('app.url')}}/product/{{$product['id']}}" class="d-block h4">{{$product['name']}}</a>

                                            @if($product['on_sale'])
                                                <del class="me-2 fs-5">{{$products['data'][0]['prices']['regular']['formatted_price']}}</del>
                                            @endif
                                            <span class="text-primary fs-5">{{$products['data'][0]['prices']['final']['formatted_price']}}</span>
                                        </div>
                                    </div>
                                    <div
                                        class="product-item-add border border-top-0 rounded-bottom  text-center p-4 pt-0">
                                        <a href="#"
                                           class="btn btn-primary border-secondary rounded-pill py-2 px-4 mb-4"><i
                                                class="fas fa-shopping-cart me-2"></i> Add To Cart</a>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex">
                                                <i class="fas fa-star text-primary"></i>
                                                <i class="fas fa-star text-primary"></i>
                                                <i class="fas fa-star text-primary"></i>
                                                <i class="fas fa-star text-primary"></i>
                                                <i class="fas fa-star"></i>
                                            </div>
                                            <div class="d-flex">
                                                <a href="#"
                                                   class="text-primary d-flex align-items-center justify-content-center me-0"><span
                                                        class="rounded-circle btn-sm-square border"><i
                                                            class="fas fa-heart"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                @endforeach
                            @endif
                            <!--end product-->
                    </div>
                            <div class="col-12 wow fadeInUp" data-wow-delay="0.1s">
                                <div class="pagination d-flex justify-content-center mt-5">
                                    @php
                                        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : $products['meta']['current_page'];
                                        $totalPages = $products['meta']['last_page']
                                    @endphp

                                        <!-- Previous Page Link -->
                                    <a href="?page={{$currentPage - 1}}" class="rounded btn {{($currentPage == 1) ? 'disabled' : ''}}">&laquo;</a>

                                    <!-- Page Number Links -->
                                    @for($i = 1; $i <= $totalPages; $i++)
                                        <a href="?page={{$i}}" class="btn {{($currentPage == $i) ? 'active' : ''}} rounded">{{$i}}</a>
                                    @endfor

                                    <!-- Next Page Link -->
                                    <a href="?page={{$currentPage + 1}}" class="btn rounded {{($currentPage == $totalPages) ? 'disabled' : ''}}">&raquo;</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Shop Page End -->
@include('partials.footer')
