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
        $products = $this->bagistoApi->getProducts();
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
        $reviews = $this->bagistoApi->getProductReviews($id);
        $categories = $this->bagistoApi->getCategories();

        return view('product', [
            'products'         => $products,
            'product'          => $product,
            'categories'       => $categories,
        ]);

    }
}
