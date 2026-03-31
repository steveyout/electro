<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Product\Repositories\ProductRepository;
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
        $homeCategories = $this->categoryRepository->scopeQuery(function ($query) {
            return $query->where('status', 1)
                ->where('parent_id', core()->getCurrentChannel()->root_category_id)
                ->orderBy('position', 'asc')
                ->limit(4);
        })->get();

        return view('index', compact('featuredProducts', 'products', 'newProducts', 'categories', 'homeCategories'));
    }

    // ... Keep your other methods (product, category, etc.) as they were
    public function product($id)
    {
        // 1. Fetch the main product
        $product = $this->productRepository->findOrFail($id);

        // 2. Define the missing variable
        // In Bagisto, this is a property/relationship on the product model
        $relatedProducts = $product->related_products;

        // 3. Fetch Featured Products (using the fix from before)
        $featuredIds = collect($this->productRepository->getAll(['featured' => 1]))->pluck('id');
        $featuredProducts = $this->productRepository->findWhereIn('products.id', $featuredIds->toArray());

        // 4. Fetch Reviews
        $reviews = $this->reviewRepository->where([
            'product_id' => $id,
            'status'     => 'approved'
        ])->get();

        // 5. Fetch Categories
        $categories = $this->categoryRepository->getVisibleCategoryTree(
            core()->getCurrentChannel()->root_category_id
        );

        // Now compact() will find the variable
        return view('product', compact(
            'product',
            'relatedProducts',
            'featuredProducts',
            'reviews',
            'categories'
        ));
    }

    public function category(Request $request, $id)
    {
        $category = $this->categoryRepository->findOrFail($id);
        $products = $this->productRepository->getAll(['category_id' => $id]);
        $categories = $this->categoryRepository->getVisibleCategoryTree(core()->getCurrentChannel()->root_category_id);

        return view('category', compact('category', 'products', 'categories'));
    }

    public function categories(Request $request)
    {
        $categories = $this->categoryRepository->getVisibleCategoryTree(core()->getCurrentChannel()->root_category_id);

        return view('categories', compact('categories'));
    }

    public function products(Request $request)
    {
        // 1. Get the filtered products for the main grid
        $products = $this->productRepository->getAll($request->all());

        // 2. Get the categories for the sidebar
        $categories = $this->categoryRepository->getVisibleCategoryTree(
            core()->getCurrentChannel()->root_category_id
        );

        // 3. Get featured products so the sidebar doesn't crash
        // Using the join fix we discussed to avoid the "Column not found" error
        $featuredProducts = $this->productRepository->scopeQuery(function ($query) {
            return $query->distinct()
                ->select('product_flat.*')
                ->join('product_flat', 'products.id', '=', 'product_flat.product_id')
                ->where('product_flat.featured', 1)
                ->where('product_flat.status', 1)
                ->where('product_flat.channel', core()->getCurrentChannel()->code)
                ->where('product_flat.locale', app()->getLocale());
        })->get();

        // 4. Pass ALL three variables to the view
        return view('products', compact('products', 'categories', 'featuredProducts'));
    }

    public function contact(Request $request)
    {
        $categories = $this->categoryRepository->getVisibleCategoryTree(core()->getCurrentChannel()->root_category_id);

        return view('contact', compact('categories'));
    }
}
