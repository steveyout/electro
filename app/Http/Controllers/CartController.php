<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Webkul\Checkout\Facades\Cart;

class CartController extends Controller
{
    public function addToCart(Request $request, $id)
    {
        try {
            // This handles sessions, taxes, and inventory automatically
            $cart = Cart::addProduct($id, [
                'product_id' => $id,
                'quantity'   => $request->input('quantity', 1)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Item added to cart.',
                'cart'    => Cart::getCart(), // Returns full cart object for your header badge
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
