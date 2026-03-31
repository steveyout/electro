<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Customer\Repositories\CustomerGroupRepository;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    public function __construct(
        protected CustomerRepository $customerRepository,
        protected CustomerGroupRepository $customerGroupRepository
    ) {
        // No middleware call here (handle it in routes/web.php as discussed)
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a login request.
     */
    public function create()
    {
        // Fix: Use request()->validate() instead of $this->validate()
        request()->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (! auth()->guard('customer')->attempt(request(['email', 'password']), request('remember'))) {
            session()->flash('error', trans('shop::app.customer.login-form.invalid-creds'));

            return redirect()->back();
        }

        if (auth()->guard('customer')->user()->status == 0) {
            auth()->guard('customer')->logout();
            session()->flash('warning', trans('shop::app.customer.login-form.not-activated'));

            return redirect()->back();
        }

        Event::dispatch('customer.after.login', auth()->guard('customer')->user());

        return redirect()->route('shop.home.index');
    }

    /**
     * Handle a registration request.
     */
    public function register()
    {
        // Fix: Use request()->validate() instead of $this->validate()
        request()->validate([
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required|email|unique:customers,email',
            'password'   => 'required|confirmed|min:6',
        ]);

        $data = request()->all();
        $data['password'] = bcrypt($data['password']);
        $data['status'] = 1;
        $data['is_verified'] = 1;

        $customerGroup = $this->customerGroupRepository->findOneByField('code', 'general');
        $data['customer_group_id'] = $customerGroup ? $customerGroup->id : 1;

        Event::dispatch('customer.registration.before');

        $customer = $this->customerRepository->create($data);

        Event::dispatch('customer.registration.after', $customer);

        auth()->guard('customer')->login($customer);

        session()->flash('success', trans('shop::app.customer.signup-form.success'));

        return redirect()->route('shop.home.index');
    }

    /**
     * Log the user out.
     */
    public function destroy()
    {
        auth()->guard('customer')->logout();

        Event::dispatch('customer.after.logout');

        return redirect()->route('customer.session.index');
    }
}
