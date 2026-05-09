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
        try {
            $cart = Cart::getCart();
            if (! $cart) return response()->json(['status' => false, 'message' => 'Cart not found']);

            // 1. Prepare common data
            $billingData = request()->input('billing');
            $paymentMethod = request()->input('payment.method', 'mpesa');
            $orderRepository = app(\Webkul\Sales\Repositories\OrderRepository::class);

            // 2. CREATE THE ORDER RECORD MANUALLY
            // Using the Model directly avoids the Repository's broken 'where' check
            $order = \Webkul\Sales\Models\Order::create([
                'increment_id'          => $orderRepository->generateIncrementId(),
                'status'                => 'pending',
                'channel_name'          => $cart->channel->name,
                'is_guest'              => 1,
                'customer_email'        => $billingData['email'],
                'customer_first_name'   => $billingData['first_name'],
                'customer_last_name'    => $billingData['last_name'],
                'shipping_method'       => 'free_free',
                'shipping_title'        => 'Free Shipping',
                'payment_title'         => 'M-Pesa',
                'shipping_description'  => 'Free Shipping',
                'total_item_count'      => $cart->items_count,
                'total_qty_ordered'     => $cart->items_qty,
                'base_currency_code'    => $cart->base_currency_code,
                'channel_currency_code' => $cart->channel_currency_code,
                'order_currency_code'   => $cart->cart_currency_code,
                'grand_total'           => $cart->grand_total,
                'base_grand_total'      => $cart->base_grand_total,
                'sub_total'             => $cart->sub_total,
                'base_sub_total'        => $cart->base_sub_total,
                'tax_amount'            => $cart->tax_total,
                'base_tax_amount'       => $cart->base_tax_total,
                'discount_amount'       => $cart->discount_amount,
                'base_discount_amount'  => $cart->base_discount_amount,
                'cart_id'               => $cart->id,
                'channel_id'            => $cart->channel_id,
            ]);

            // 3. CREATE ORDER ITEMS MANUALLY
            foreach ($cart->items as $item) {
                \Webkul\Sales\Models\OrderItem::create([
                    'order_id'    => $order->id,
                    'product_id'  => $item->product_id,
                    'sku'         => $item->sku,
                    'type'        => $item->type,
                    'name'        => $item->name,
                    'weight'      => $item->weight,
                    'price'       => $item->price,
                    'base_price'  => $item->base_price,
                    'total'       => $item->total,
                    'base_total'  => $item->base_total,
                    'qty_ordered' => $item->quantity,
                    'additional'  => $item->additional,
                ]);
            }

            // 4. CREATE ORDER ADDRESSES
            $addressData = array_merge($billingData, [
                'order_id'     => $order->id,
                'address1'     => is_array($billingData['address1']) ? implode(PHP_EOL, $billingData['address1']) : $billingData['address1'],
                'address_type' => 'billing'
            ]);

            \Webkul\Sales\Models\OrderAddress::create($addressData);
            \Webkul\Sales\Models\OrderAddress::create(array_merge($addressData, ['address_type' => 'shipping']));

            // 5. CREATE ORDER PAYMENT
            \Webkul\Sales\Models\OrderPayment::create([
                'order_id' => $order->id,
                'method'   => $paymentMethod,
            ]);

            Cart::deActivateCart();

            return response()->json([
                'status'       => true,
                'order_id'     => $order->id,
                'redirect_url' => route('shop.checkout.success'),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Local Model Error: ' . $e->getMessage()
            ]);
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
