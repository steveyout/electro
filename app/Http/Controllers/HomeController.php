<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Product\Repositories\ProductReviewRepository;

class HomeController extends Controller
{
    public function __construct(
        protected ProductRepository $productRepository,
        protected CategoryRepository $categoryRepository,
        protected ProductReviewRepository $reviewRepository
    ) {}

    public function index(Request $request)
    {
        // 1. Get products for the main sections
        $products = $this->productRepository->getAll($request->all());
        $featuredProducts = $this->productRepository->getAll(['featured' => 1, 'limit' => 12]);
        $newProducts = $this->productRepository->getAll(['new' => 1, 'limit' => 12]);

        // 2. Get the Tree (Used for your Navbar/Search dropdown)
        $categories = $this->categoryRepository->getVisibleCategoryTree(
            core()->getCurrentChannel()->root_category_id
        );

        // 3. Get exactly 4 ACTIVE categories for the homepage carousels
        // We query the database directly for siblings of the root category
        $homeCategories = $this->categoryRepository->scopeQuery(function($query) {
            return $query->where('status', 1)
                ->where('parent_id', core()->getCurrentChannel()->root_category_id)
                ->orderBy('position', 'asc')
                ->limit(4);
        })->get();

        return view('index', compact('featuredProducts', 'products', 'newProducts', 'categories', 'homeCategories'));
    }

    // ... Keep your other methods (product, category, etc.) as they were
    public function product(Request $request, $id) {
        $product = $this->productRepository->findOrFail($id);
        $reviews = $this->reviewRepository->findByField(['product_id' => $id, 'status' => 'approved']);
        $categories = $this->categoryRepository->getVisibleCategoryTree(core()->getCurrentChannel()->root_category_id);
        return view('product', ['product' => $product, 'relatedProducts' => $product->related_products, 'reviews' => $reviews, 'categories' => $categories]);
    }

    public function category(Request $request, $id) {
        $category = $this->categoryRepository->findOrFail($id);
        $products = $this->productRepository->getAll(['category_id' => $id]);
        $categories = $this->categoryRepository->getVisibleCategoryTree(core()->getCurrentChannel()->root_category_id);
        return view('category', compact('category', 'products', 'categories'));
    }

    public function categories(Request $request) {
        $categories = $this->categoryRepository->getVisibleCategoryTree(core()->getCurrentChannel()->root_category_id);
        return view('categories', compact('categories'));
    }

    public function products(Request $request) {
        $products = $this->productRepository->getAll($request->all());
        $categories = $this->categoryRepository->getVisibleCategoryTree(core()->getCurrentChannel()->root_category_id);
        return view('products', compact('products', 'categories'));
    }

    public function contact(Request $request) {
        $categories = $this->categoryRepository->getVisibleCategoryTree(core()->getCurrentChannel()->root_category_id);
        return view('contact', compact('categories'));
    }
}
