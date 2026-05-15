<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Webkul\Sales\Repositories\OrderRepository;

class OrderController extends Controller
{
    /**
     * OrderRepository object
     *
     * @var \Webkul\Sales\Repositories\OrderRepository
     */
    protected $orderRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Sales\Repositories\OrderRepository  $orderRepository
     * @return void
     */
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Display a listing of the customer's orders.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $orders = $this->orderRepository->scopeQuery(function ($query) {
            return $query->where('customer_id', auth()->guard('customer')->user()->id)
                ->orderBy('id', 'desc');
        })->paginate(10);

        return view('customer.orders', compact('orders'));
    }

    /**
     * Show a specific order detail page.
     *
     * @param int $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function view(int $id)
    {
        // Fetch the order and ensure it belongs to the authenticated customer
        $order = $this->orderRepository->findOneByField([
            'id'          => $id,
            'customer_id' => auth()->guard('customer')->user()->id,
        ]);

        if (! $order) {
            abort(404, 'Order not found or access denied.');
        }

        return view('customer.order', compact('order'));
    }
}
