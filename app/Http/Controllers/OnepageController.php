<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Webkul\Checkout\Facades\Cart;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Payment\Facades\Payment;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Shipping\Facades\Shipping;

class OnepageController extends Controller
{
    public function __construct(
        protected OrderRepository $orderRepository,
        protected CustomerRepository $customerRepository
    ) {}

    /**
     * Display the checkout page.
     */
    public function index()
    {
        $cart = Cart::getCart();

        if (! $cart || ! $cart->items->count()) {
            return redirect()->route('shop.checkout.cart.index');
        }

        return view('checkout.index', compact('cart'));
    }

    /**
     * Save Address and return available shipping methods.
     */
    public function saveAddress(Request $request)
    {
        // Simple validation for your orange theme form
        $request->validate([
            'billing.first_name' => 'required',
            'billing.address1'   => 'required',
            'billing.phone'      => 'required',
        ]);

        $data = $request->all();
        $data['billing']['address1'] = implode(PHP_EOL, array_filter([$data['billing']['address1']]));
        $data['shipping'] = $data['billing']; // Assume shipping is same as billing

        if (! Cart::saveCustomerAddress($data)) {
            return response()->json(['status' => false], 400);
        }

        // Trigger Bagisto to calculate shipping rates based on the address
        Shipping::collectRates();

        return response()->json([
            'status'          => true,
            'jump_to_section' => 'shipping',
            'html'            => view('shop.checkout.shipping-methods')->render(),
        ]);
    }

    /**
     * Save Shipping Method (e.g. Flat Rate)
     */
    public function saveShipping(Request $request)
    {
        $shippingMethod = $request->get('shipping_method');

        if (! Cart::saveShippingMethod($shippingMethod)) {
            return response()->json(['status' => false], 400);
        }

        return response()->json([
            'status'          => true,
            'jump_to_section' => 'payment',
        ]);
    }

    /**
     * Save Payment Method (Pay on Delivery or M-Pesa)
     */
    public function savePayment(Request $request)
    {
        $payment = $request->get('payment');

        if (! Cart::savePaymentMethod($payment)) {
            return response()->json(['status' => false], 400);
        }

        return response()->json([
            'status'          => true,
            'jump_to_section' => 'review',
        ]);
    }

    /**
     * Final step: Create the Order
     */
    public function saveOrder()
    {
        if (Cart::hasError()) {
            return response()->json(['status' => false, 'message' => 'Cart has errors.'], 400);
        }

        Cart::collectTotals();

        try {
            $this->validateOrder();

            $cart = Cart::getCart();

            // 1. Prepare and Create the Order
            $orderData = Cart::prepareDataForOrder();
            $order = $this->orderRepository->create($orderData);

            // 2. Store Order Details in Session for the Success Page
            session()->put('last_order_id', $order->id);
            session()->put('order_total', $order->base_grand_total);
            session()->put('payment_method', $order->payment->method_title);

            // 3. Clear the cart session
            Cart::deActivateCart();

            return response()->json([
                'status'       => true,
                'order_id'     => $order->id,
                'redirect_url' => route('shop.checkout.success'),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Order creation failed: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Success Page
     */
    public function success()
    {
        return view('checkout.success');
    }

    protected function validateOrder()
    {
        $cart = Cart::getCart();

        if (! $cart->shipping_address || ! $cart->billing_address || ! $cart->payment) {
            throw new \Exception('Missing checkout steps.');
        }
    }
}
