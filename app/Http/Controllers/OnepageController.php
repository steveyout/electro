<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Log;
use Webkul\Checkout\Facades\Cart;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Payment\Facades\Payment;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Models\OrderAddress;
use Webkul\Sales\Models\OrderPayment;
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
    public function saveOrder(Request $request)
    {
        try {
            $cart = Cart::getCart();
            if (! $cart) {
                return response()->json(['status' => false, 'message' => 'Cart not found']);
            }

            $billingData = $request->input('billing');
            $address1 = is_array($billingData['address1'])
                ? implode(PHP_EOL, array_filter($billingData['address1']))
                : $billingData['address1'];

            // 1. Manually Map Order Data (replacing the missing prepareDataFromCart)
            $orderData = [
                'cart_id'               => $cart->id,
                'customer_id'           => $cart->customer_id,
                'is_guest'              => $cart->is_guest,
                'customer_email'        => $cart->customer_email,
                'customer_first_name'   => $cart->customer_first_name,
                'customer_last_name'    => $cart->customer_last_name,
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
                'shipping_amount'       => $cart->selected_shipping_rate->price ?? 0,
                'base_shipping_amount'  => $cart->selected_shipping_rate->base_price ?? 0,
                'shipping_method'       => $cart->selected_shipping_rate->method ?? 'flatrate_flatrate',
                'shipping_title'        => $cart->selected_shipping_rate->method_title ?? 'Flat Rate',
                'channel_id'            => $cart->channel_id,
                'channel_type'          => 'Webkul\Core\Models\Channel',
                'status'                => 'pending',
                'increment_id'          => $this->orderRepository->generateIncrementId(),
            ];

            DB::beginTransaction();

            // 2. Create Order using the actual Model class (bypass Proxy)
            $order = \Webkul\Sales\Models\Order::create($orderData);

            // 3. Create Order Items manually
            foreach ($cart->items as $item) {
                \Webkul\Sales\Models\OrderItem::create([
                    'order_id'             => $order->id,
                    'product_id'           => $item->product_id,
                    'product_type'         => $item->type == 'configurable' ? 'Webkul\Product\Models\Product' : $item->product_var_type ?? 'Webkul\Product\Models\Product',
                    'sku'                  => $item->sku,
                    'type'                 => $item->type,
                    'name'                 => $item->name,
                    'additional'           => $item->additional,
                    'qty_ordered'          => $item->quantity,
                    'price'                => $item->price,
                    'base_price'           => $item->base_price,
                    'total'                => $item->total,
                    'base_total'           => $item->base_total,
                    'weight'               => $item->weight,
                    'total_weight'         => $item->total_weight,
                    'tax_percent'          => $item->tax_percent,
                    'tax_amount'           => $item->tax_amount,
                    'base_tax_amount'      => $item->base_tax_amount,
                    'discount_percent'     => $item->discount_percent,
                    'discount_amount'      => $item->discount_amount,
                    'base_discount_amount' => $item->base_discount_amount,
                ]);
            }

            // 4. Create Addresses manually
            $addressPayload = [
                'order_id'     => $order->id,
                'first_name'   => $billingData['first_name'],
                'last_name'    => $billingData['last_name'],
                'email'        => $billingData['email'],
                'address1'     => $address1,
                'city'         => $billingData['city'] ?? 'Nairobi',
                'state'        => $billingData['state'] ?? 'KE',
                'country'      => $billingData['country'] ?? 'KE',
                'postcode'     => $billingData['postcode'] ?? '00100',
                'phone'        => $billingData['phone'],
            ];

            OrderAddress::create(array_merge($addressPayload, [
                'address_type' => 'order_billing',
            ]));

            OrderAddress::create(array_merge($addressPayload, [
                'address_type' => 'order_shipping',
            ]));

            // 5. Create Payment record manually (M-Pesa)
            OrderPayment::create([
                'order_id' => $order->id,
                'method'   => 'mpesa',
            ]);

            DB::commit();

            // 6. Success and Redirect
            // 6. PERSISTENT SESSION STORAGE
            // We use 'put' instead of 'flash' to ensure data survives redirects
            session()->put('last_order_id', $order->increment_id);
            session()->put('order_total', $order->grand_total);
            Cart::deActivateCart();

            return response()->json([
                'status'       => true,
                'redirect_url' => route('shop.checkout.success'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Manual Checkout Final Fix: '.$e->getMessage());

            return response()->json([
                'status'  => false,
                'message' => 'Manual Finalization Error: '.$e->getMessage(),
            ], 500);
        }
    }

    public function success()
    {
        $order = Order::find(session('last_order_id'));
        if (! $order) {
            return redirect()->route('shop.home.index');
        }

        return view('checkout.success', compact('order'));
    }

    protected function validateOrder()
    {
        $cart = Cart::getCart();

        if (! $cart->shipping_address || ! $cart->billing_address || ! $cart->payment) {
            throw new \Exception('Missing checkout steps.');
        }
    }
}
