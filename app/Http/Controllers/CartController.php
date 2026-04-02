<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Webkul\Checkout\Facades\Cart;
use Webkul\Product\Repositories\ProductRepository;

class CartController extends Controller
{
    public function __construct(protected ProductRepository $productRepository) {}

    /**
     * Display the shopping cart page.
     */
    public function index()
    {
        $cart = Cart::getCart();

        return view('checkout.cart', compact('cart'));
    }

    /**
     * Add a product to the cart.
     */
    public function add($id)
    {
        try {
            $product = $this->productRepository->find($id);

            if (! $product) {
                return response()->json(['status' => 'error', 'message' => 'Product not found.'], 404);
            }

            // Using Bagisto's addProduct logic
            $cart = Cart::addProduct($product->id, [
                'quantity'   => request()->get('quantity', 1),
                'product_id' => $product->id,
            ]);

            if (! $cart) {
                return response()->json(['status' => 'error', 'message' => 'Could not add product.'], 400);
            }

            return response()->json([
                'status'     => 'success',
                'message'    => 'Item added to cart!',
                'cart_count' => Cart::getCart()->items_count,
                'cart_total' => core()->currency(Cart::getCart()->base_grand_total),
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove an item from the cart.
     */
    public function remove($id)
    {
        try {
            Cart::removeItem($id);

            if (request()->ajax()) {
                return response()->json([
                    'status'  => 'success',
                    'message' => 'Item removed successfully.',
                    'cart'    => Cart::getCart(),
                ]);
            }

            return redirect()->back()->with('success', 'Item removed.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Could not remove item.');
        }
    }

    /**
     * Update quantities in the cart.
     */
    public function update(Request $request)
    {
        try {
            // Bagisto expects an array: ['qty' => [item_id => quantity]]
            $data = $request->input('qty');

            if (! $data) {
                return redirect()->back();
            }

            Cart::updateItems(['qty' => $data]);

            if ($request->ajax()) {
                return response()->json([
                    'status'  => 'success',
                    'message' => 'Cart updated.',
                    'cart'    => Cart::getCart(),
                ]);
            }

            return redirect()->route('shop.checkout.cart.index')->with('success', 'Cart updated.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Helpful for refreshing Mini-Cart or Bottom Nav via JS.
     */
    public function getMiniCart()
    {
        $cart = Cart::getCart();

        return response()->json([
            'status'     => 'success',
            'cart_count' => $cart ? $cart->items_count : 0,
            'cart_total' => core()->currency($cart ? $cart->base_grand_total : 0),
            'html'       => view('partials.mini-cart-items', compact('cart'))->render(),
        ]);
    }
}
