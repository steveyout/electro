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
        if (! $id) {
            throw new Exception('no product id provided!');
        }

        $csrfToken = $request->cookie('XSRF-TOKEN');

        $parameters=[
            'product_id'=>$id,
            'is_buy_now'=>0,
            'quantity'=>1
        ];
        $cart = $this->bagistoApi->addToCart($id, $csrfToken,$parameters);

        return response()->json([
            'success'    => true,
            'cart'       => $cart,
        ]);

    }
}
