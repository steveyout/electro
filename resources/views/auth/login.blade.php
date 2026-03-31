@include('partials.header')

<div class="container-fluid py-5" style="background: #f8f9fa;">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-4 col-md-7">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-sm-5">
                        <div class="text-center mb-4">
                            <h3 class="fw-bold text-dark">Welcome Back</h3>
                            <p class="text-muted small">Login to manage your orders & wishlist</p>
                        </div>

                        @if ($message = session('error'))
                            <div class="alert alert-danger small py-2">{{ $message }}</div>
                        @endif

                        <form action="{{ route('customer.session.create') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Email Address</label>
                                <input type="email" name="email" class="form-control form-control-lg fs-6"
                                       placeholder="name@example.com" value="{{ old('email') }}" required>
                                @error('email') <span class="text-danger extra-small">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label small fw-bold text-muted">Password</label>
                                    <a href="#" class="text-primary small text-decoration-none">Forgot?</a>
                                </div>
                                <input type="password" name="password" class="form-control form-control-lg fs-6"
                                       placeholder="••••••••" required>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label small text-muted" for="remember">
                                    Keep me logged in
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 mb-3 fw-bold shadow-sm">
                                LOGIN <i class="fas fa-sign-in-alt ms-2"></i>
                            </button>

                            <div class="text-center mt-3">
                                <p class="small text-muted mb-0">New to {{ config('app.name') }}?</p>
                                <a href="{{ route('customer.register.index') }}" class="text-primary fw-bold text-decoration-none">Create an Account</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials.footer')
