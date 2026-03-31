<?php

namespace App\Http\Controllers;

use Webkul\Checkout\Facades\Cart;
use Webkul\Product\Repositories\ProductRepository;

class CartController extends Controller
{
    /**
     * @return void
     */
    public function __construct(protected ProductRepository $productRepository) {}

    public function add($id)
    {
        try {
            // 1. Find the actual Product Object using the ID
            $product = $this->productRepository->find($id);

            if (! $product) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Product not found.',
                ], 404);
            }

            // 2. Pass the Product OBJECT (not just the ID) to addProduct
            $cart = Cart::addProduct($product, [
                'quantity'   => request()->get('quantity', 1),
                'product_id' => $id,
            ]);

            if (! $cart) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Could not add product to cart.',
                ], 400);
            }

            return response()->json([
                'status'     => 'success',
                'message'    => 'Item added to cart!',
                'cart_count' => Cart::getCart()->items_count,
                'cart_total' => core()->currency(Cart::getCart()->base_grand_total),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
