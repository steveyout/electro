<?php

namespace App\Http\Controllers;

use App\Services\BagistoApiService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected BagistoApiService $bagistoApi;

    public function __construct(BagistoApiService $bagistoApiService)
    {
        $this->bagistoApi = $bagistoApiService;
    }

    // //cart page
    public function addToCart(Request $request, $id)
    {
        global $response;
        if (! $id) {
            throw new Exception('no product id provided!');
        }
        $cart = $this->bagistoApi->addToCart($id);

        return $response->json([
            'success'    => true,
            'cart'       => $cart,
        ]);

    }
}
