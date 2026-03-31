<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Product\Repositories\ProductRepository;

class SearchController extends Controller
{
    /**
     * ProductRepository object
     *
     * @var \Webkul\Product\Repositories\ProductRepository
     */
    protected $productRepository;

    /**
     * CategoryRepository object
     *
     * @var \Webkul\Category\Repositories\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Index to display the search results.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $term = $request->input('term');
        $categoryId = $request->input('category');
        $priceMax = $request->input('price_max');

        $products = $this->productRepository->scopeQuery(function ($query) use ($term, $categoryId, $priceMax) {
            $channel = core()->getCurrentChannel()->code;
            $locale = app()->getLocale();

            $query = $query->distinct()
                ->select('product_flat.*')
                ->join('product_flat', 'products.id', '=', 'product_flat.product_id')
                ->where('product_flat.status', 1)
                ->where('product_flat.channel', $channel)
                ->where('product_flat.locale', $locale);

            // 1. Strict Search Term Filter
            if (! empty($term)) {
                $query->where(function ($q) use ($term) {
                    $q->where('product_flat.name', 'like', '%'.$term.'%')
                        ->orWhere('product_flat.short_description', 'like', '%'.$term.'%')
                        ->orWhere('product_flat.sku', 'like', '%'.$term.'%');
                });
            }

            // 2. Strict Category Filter
            if (! empty($categoryId)) {
                $query->join('product_categories', 'products.id', '=', 'product_categories.product_id')
                    ->where('product_categories.category_id', $categoryId);
            }

            // 3. Price Filter - Using 'price' instead of 'min_price'
            if (! empty($priceMax)) {
                $query->where('product_flat.price', '<=', $priceMax);
            }

            return $query;
        })->paginate(12);

        // Categories for sidebar
        $categories = $this->categoryRepository->getVisibleCategoryTree(core()->getCurrentChannel()->root_category_id);

        // Featured products for sidebar
        $featuredProducts = $this->productRepository->scopeQuery(function ($query) {
            return $query->distinct()
                ->select('product_flat.*')
                ->join('product_flat', 'products.id', '=', 'product_flat.product_id')
                ->where('product_flat.featured', 1)
                ->where('product_flat.status', 1)
                ->where('product_flat.channel', core()->getCurrentChannel()->code)
                ->where('product_flat.locale', app()->getLocale());
        })->get();

        return view('products', compact('products', 'categories', 'featuredProducts'));
    }
}
