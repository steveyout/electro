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
            // 1. Fetch the Product Object
            $product = $this->productRepository->find($id);

            if (! $product) {
                return response()->json(['status' => 'error', 'message' => 'Product not found.'], 404);
            }

            // 2. Add product to cart
            $cart = Cart::addProduct($product, [
                'quantity'   => request()->get('quantity', 1),
                'product_id' => $product->id,
            ]);

            if (is_array($cart) && isset($cart['error'])) {
                return response()->json(['status' => 'error', 'message' => $cart['error']], 400);
            }

            // 3. Return updated cart details + Rendered HTML for the drawer
            $currentCart = Cart::getCart();

            return response()->json([
                'status'     => 'success',
                'message'    => 'Item added to cart!',
                'cart_count' => $currentCart ? $currentCart->items_count : 0,
                'cart_total' => $currentCart ? core()->currency($currentCart->base_grand_total) : 0,
                // render() converts the blade view into a string of HTML
                'cart_html'  => view('checkout.mini-cart', ['cart' => $currentCart])->render(),
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
            // 1. Remove the item
            Cart::removeItem($id);

            // 2. FORCE REFRESH: This is the critical part
            // It forces Bagisto to recalculate totals and sync the session
            Cart::collectTotals();

            $currentCart = Cart::getCart();

            // If the cart is now empty, we ensure we return zeros
            if (! $currentCart || $currentCart->items->count() == 0) {
                return response()->json([
                    'status'     => 'success',
                    'cart_count' => 0,
                    'cart_total' => core()->currency(0),
                ]);
            }

            return response()->json([
                'status'     => 'success',
                'cart_count' => $currentCart->items_count,
                'cart_total' => core()->currency($currentCart->base_grand_total),
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
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
