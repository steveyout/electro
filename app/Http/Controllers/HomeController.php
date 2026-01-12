<?php

namespace App\Http\Controllers;

use App\Services\BagistoApiService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected BagistoApiService $bagistoApi;

    public function __construct(BagistoApiService $bagistoApiService)
    {
        $this->bagistoApi = $bagistoApiService;
    }

    // home controller
    public function index(Request $request)
    {
        $products = $this->bagistoApi->getFeaturedProducts();
        $categories = $this->bagistoApi->getCategories();

        return view('index', [
            'featuredProducts' => $products,
            'products'         => $products,
            'newProducts'      => $products,
            'allProducts'      => $products,
            'topProducts'      => $products,
            'categories'       => $categories,
        ]);

    }
}
