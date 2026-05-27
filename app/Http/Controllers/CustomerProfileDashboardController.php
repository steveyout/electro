<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Webkul\Customer\Repositories\CustomerAddressRepository;
use Webkul\Customer\Repositories\CustomerRepository;

class CustomerProfileDashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected CustomerRepository $customerRepository,
        protected CustomerAddressRepository $customerAddressRepository
    ) {}

    /**
     * Display the customer profile overview dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $customer = auth()->guard('customer')->user();

        if (!$customer) {
            return redirect()->route('customer.session.index');
        }

        $customer->default_address = $customer->addresses()
            ->where('default_address', 1)
            ->first();

        return view('customer.profile', compact('customer'));
    }

    /**
     * Handle Async Customer Profile Picture Updates.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function avatar(Request $request)
    {
        $customer = auth()->guard('customer')->user();

        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action context.'], 401);
        }

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        if ($request->hasFile('avatar')) {
            // Delete old file using your exact column name 'image'
            if ($customer->image) {
                Storage::disk('public')->delete($customer->image);
            }

            $path = $request->file('avatar')->store('customer/avatar/' . $customer->id, 'public');

            // Save straight to your database field 'image'
            $customer->image = $path;
            $customer->save();

            // FIXED: Fired against the Repository layer instance context directly
            if (method_exists($this->customerRepository, 'cleanCache')) {
                $this->customerRepository->cleanCache();
            }

            return response()->json([
                'success'    => true,
                'message'    => 'Profile picture updated successfully!',
                'avatar_url' => asset('storage/' . $path)
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid file payload resource.'], 400);
    }

    /**
     * Store/Update the customer profile parameters.
     */
    public function store(Request $request)
    {
        $customer = auth()->guard('customer')->user();

        if (!$customer) {
            return redirect()->route('customer.session.index');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:customers,email,'.$customer->id,
            'phone'      => 'nullable|string|max:20',
            'address1'   => 'required|string|max:255',
            'city'       => 'required|string|max:255',
            'state'      => 'required|string|max:255',
            'postcode'   => 'required|string|max:20',
        ]);

        $data = $request->all();

        Event::dispatch('customer.profile.update.before', $customer->id);

        $this->customerRepository->update([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'],
        ], $customer->id);

        Event::dispatch('customer.profile.update.after', $customer->id);

        $defaultAddress = $customer->addresses()->where('default_address', 1)->first();

        $addressData = [
            'customer_id'     => $customer->id,
            'first_name'      => $data['first_name'],
            'last_name'       => $data['last_name'],
            'address1'        => $data['address1'],
            'city'            => $data['city'],
            'state'           => $data['state'],
            'postcode'        => $data['postcode'],
            'phone'           => $data['phone'],
            'default_address' => 1,
        ];

        if ($defaultAddress) {
            $this->customerAddressRepository->update($addressData, $defaultAddress->id);
        } else {
            $this->customerAddressRepository->create($addressData);
        }

        session()->flash('success', trans('shop::app.customer.account.profile.index.edit-success'));

        return redirect()->back();
    }
}
