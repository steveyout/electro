@include('partials.header')

<div class="container-fluid py-5" style="background: #f8f9fa;">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-sm-5">
                        <div class="text-center mb-4">
                            <h3 class="fw-bold text-dark">Join Us</h3>
                            <p class="text-muted small">Create an account to start shopping</p>
                        </div>

                        <form action="{{ route('customer.register.create') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-muted">First Name</label>
                                    <input type="text" name="first_name" class="form-control"
                                           placeholder="John" value="{{ old('first_name') }}" required>
                                    @error('first_name') <span class="text-danger extra-small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-muted">Last Name</label>
                                    <input type="text" name="last_name" class="form-control"
                                           placeholder="Doe" value="{{ old('last_name') }}" required>
                                    @error('last_name') <span class="text-danger extra-small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Email Address</label>
                                <input type="email" name="email" class="form-control"
                                       placeholder="john@example.com" value="{{ old('email') }}" required>
                                @error('email') <span class="text-danger extra-small">{{ $message }}</span> @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-muted">Password</label>
                                    <input type="password" name="password" class="form-control"
                                           placeholder="••••••••" required>
                                    @error('password') <span class="text-danger extra-small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold text-muted">Confirm Password</label>
                                    <input type="password" name="password_confirmation" class="form-control"
                                           placeholder="••••••••" required>
                                </div>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label small text-muted" for="terms">
                                    I agree to the <a href="#" class="text-primary text-decoration-none">Terms & Conditions</a>
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 mb-3 fw-bold shadow-sm">
                                CREATE ACCOUNT <i class="fas fa-user-plus ms-2"></i>
                            </button>

                            <div class="text-center mt-3">
                                <p class="small text-muted mb-0">Already have an account?</p>
                                <a href="{{ route('customer.session.index') }}" class="text-primary fw-bold text-decoration-none">Login Here</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials.footer')
