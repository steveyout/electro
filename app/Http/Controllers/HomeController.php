<?php

namespace App\Http\Controllers;

use App\Services\BagistoApiService;
use Exception;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected BagistoApiService $bagistoApi;

    public function __construct(BagistoApiService $bagistoApiService)
    {
        $this->bagistoApi = $bagistoApiService;
    }

    // index
    public function index(Request $request)
    {
        $products = $this->bagistoApi->getProducts([
            'limit'=> 12,
            'page' => $request->query('page', 1),
        ]);
        $featuredProducts = $this->bagistoApi->getFeaturedProducts();
        $newProducts = $this->bagistoApi->getNewProducts();
        $categories = $this->bagistoApi->getCategories();

        return view('index', [
            'featuredProducts' => $featuredProducts,
            'products'         => $products,
            'newProducts'      => $newProducts,
            'categories'       => $categories,
        ]);

    }

    // ////////////get product
    public function product(Request $request, $id)
    {
        if (! $id) {
            throw new Exception('no product id provided!');
        }
        $products = $this->bagistoApi->getFeaturedProducts();
        $product = $this->bagistoApi->getProduct($id);
        $relatedProducts = $this->bagistoApi->getRelatedProducts($id);
        $reviews = $this->bagistoApi->getProductReviews($id);
        $categories = $this->bagistoApi->getCategories();

        return view('product', [
            'products'         => $products,
            'product'          => $product,
            'categories'       => $categories,
            'relatedProducts'  => $relatedProducts,
            'reviews'          => $reviews,
        ]);

    }

    // //category page
    public function category(Request $request, $id)
    {
        if (! $id) {
            throw new Exception('no category id provided!');
        }
        $categories = $this->bagistoApi->getCategories();

        return view('category', [
            'categories'       => $categories,
        ]);

    }

    // //all categories page
    public function categories(Request $request)
    {
        $categories = $this->bagistoApi->getCategories();

        return view('categories', [
            'categories'       => $categories,
        ]);

    }

    // //all products page
    public function products(Request $request)
    {
        $categories = $this->bagistoApi->getCategories();
        $featuredProducts = $this->bagistoApi->getFeaturedProducts();
        $products = $this->bagistoApi->getProducts([
            'limit'=> 12,
            'page' => $request->query('page', 1),
        ]);

        return view('products', [
            'categories'       => $categories,
            'featuredProducts' => $featuredProducts,
            'products'         => $products,
        ]);

    }

    // //contact us page
    public function contact(Request $request)
    {
        $categories = $this->bagistoApi->getCategories();

        return view('contact', [
            'categories'       => $categories,
        ]);

    }
}
