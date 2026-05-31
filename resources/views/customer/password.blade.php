@include('partials.header')

<div class="container-fluid py-5 bg-light">
    <div class="container">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0 fw-bold">Security <span class="text-primary">Center</span></h2>
            <a href="{{ route('customer.profile.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i>Back to Profile
            </a>
        </div>

        <div class="row g-4">
            {{-- Left Column: Sidebar (Reuse logic) --}}
            <div class="col-lg-4 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white p-4">
                    <div class="list-group list-group-flush profile-sidebar-nav">
                        <a href="{{ route('customer.profile.index') }}" class="list-group-item list-group-item-action border-0 py-3 rounded-3 d-flex align-items-center gap-3">
                            <i class="fas fa-user-circle text-muted" style="width: 20px;"></i>
                            <span class="small fw-semibold">Account Overview</span>
                        </a>
                        <a href="{{ route('customer.orders.index') }}" class="list-group-item list-group-item-action border-0 py-3 rounded-3 d-flex align-items-center gap-3">
                            <i class="fas fa-shopping-bag text-muted" style="width: 20px;"></i>
                            <span class="small fw-semibold">Order History</span>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action border-0 py-3 rounded-3 active d-flex align-items-center gap-3">
                            <i class="fas fa-shield-alt text-primary" style="width: 20px;"></i>
                            <span class="small fw-bold">Password & Security</span>
                        </a>
                        <a href="{{ route('customer.session.destroy') }}" class="list-group-item list-group-item-action border-0 py-3 rounded-3 d-flex align-items-center gap-3 text-danger"
                           onclick="event.preventDefault(); document.getElementById('customer-logout-form').submit();">
                            <i class="fas fa-sign-out-alt" style="width: 20px;"></i>
                            <span class="small fw-semibold">Sign Out</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Right Column: Password Form --}}
            <div class="col-lg-8 col-xl-9">
                <div class="card border-0 shadow-sm rounded-3 bg-white p-4 p-md-5">
                    <div class="border-bottom pb-3 mb-4">
                        <h4 class="fw-bold text-dark m-0">Change Password</h4>
                        <p class="text-muted small m-0 mt-1">Ensure your account is using a long, random password to stay secure.</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session()->has('success'))
                        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
                            {{ session()->get('success') }}
                        </div>
                    @endif

                    <form action="{{ route('customer.profile.update.password') }}" method="POST" autocomplete="off">
                        @csrf
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label text-secondary small fw-bold mb-2">Current Password</label>
                                <input type="password" name="oldpassword" class="form-control custom-input py-25" placeholder="Enter current password" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-secondary small fw-bold mb-2">New Password</label>
                                <input type="password" name="password" class="form-control custom-input py-25" placeholder="New password" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-secondary small fw-bold mb-2">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="form-control custom-input py-25" placeholder="Confirm new password" required>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-end gap-3 border-top pt-4 mt-5">
                            <button type="reset" class="btn btn-light rounded-pill px-4 py-2 fw-semibold text-secondary small">
                                Clear
                            </button>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold small shadow-sm">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials.footer')
