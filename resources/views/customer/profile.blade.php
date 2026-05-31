@include('partials.header')

<div class="container-fluid py-5 bg-light">
    <div class="container">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0 fw-bold">My <span class="text-primary">Account</span></h2>
            <a href="{{ url('/') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i>Back to Shop
            </a>
        </div>

        <div class="row g-4">

            {{-- Left Column: Profile Card & Sidebar Navigation --}}
            <div class="col-lg-4 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white text-center p-4">

                    {{-- Avatar Block Node with AJAX Upload Control Trigger --}}
                    <div class="position-relative d-inline-block mx-auto mb-3">
                        <div class="avatar-wrapper position-relative rounded-circle overflow-hidden border border-3 border-light shadow-sm" style="width: 120px; height: 120px;">

                            @php
                                $avatarUrl = 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=256';

                                // FIXED: Accessing the authentic model storage mapping field 'image'
                                if (!empty($customer->image)) {
                                    if (filter_var($customer->image, FILTER_VALIDATE_URL)) {
                                        $avatarUrl = $customer->image;
                                    } else {
                                        $avatarUrl = asset('storage/' . $customer->image) . '?t=' . time();
                                    }
                                } elseif (!empty($customer->avatar_url)) {
                                    $avatarUrl = $customer->avatar_url . '?t=' . time();
                                }
                            @endphp

                            <img id="profileAvatarPreview"
                                 src="{{ $avatarUrl }}"
                                 class="img-fluid object-fit-cover w-100 h-100"
                                 alt="User Avatar">

                            {{-- Soft Progress Loading Spinner Overlay --}}
                            <div id="avatarSpinner" class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-none align-items-center justify-content-center text-white">
                                <i class="fas fa-spinner fa-spin fa-lg"></i>
                            </div>
                        </div>

                        {{-- Trigger Button --}}
                        <button type="button" id="avatarUploadBtn" class="btn btn-primary btn-sm rounded-circle position-absolute bottom-0 end-0 d-flex align-items-center justify-content-center shadow"
                                style="width: 32px; height: 32px; z-index: 2;"
                                title="Update Photo">
                            <i class="fas fa-camera" style="font-size: 11px;"></i>
                        </button>

                        {{-- Hidden File Upload Payload Selector --}}
                        <input type="file" id="avatarFileInput" accept="image/*" style="display: none;">
                    </div>

                    {{-- User Meta Details --}}
                    <h5 class="fw-bold mb-1 text-dark">{{ $customer->first_name }} {{ $customer->last_name }}</h5>
                    <p class="text-muted small mb-3">Member since {{ $customer->created_at ? $customer->created_at->format('M Y') : '2026' }}</p>
                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 small fw-semibold mb-4">
                        Active Account
                    </span>

                    <hr class="text-muted opacity-25 my-2">

                    {{-- Profile Menu Control Navigation Links --}}
                    <div class="list-group list-group-flush text-start profile-sidebar-nav">
                        <a href="{{ route('customer.profile.index') }}" class="list-group-item list-group-item-action border-0 py-3 rounded-3 active d-flex align-items-center gap-3">
                            <i class="fas fa-user-circle text-primary" style="width: 20px;"></i>
                            <span class="small fw-bold">Account Overview</span>
                        </a>
                        <a href="{{ route('customer.orders.index') }}" class="list-group-item list-group-item-action border-0 py-3 rounded-3 d-flex align-items-center gap-3">
                            <i class="fas fa-shopping-bag text-muted" style="width: 20px;"></i>
                            <span class="small fw-semibold">Order History</span>
                        </a>
                        <a href="{{route('customer.profile.update.password')}}" class="list-group-item list-group-item-action border-0 py-3 rounded-3 d-flex align-items-center gap-3">
                            <i class="fas fa-shield-alt text-muted" style="width: 20px;"></i>
                            <span class="small fw-semibold">Password & Security</span>
                        </a>
                        <a href="{{ route('customer.session.destroy') }}" class="list-group-item list-group-item-action border-0 py-3 rounded-3 d-flex align-items-center gap-3 text-danger"
                           onclick="event.preventDefault(); document.getElementById('customer-logout-form').submit();">
                            <i class="fas fa-sign-out-alt" style="width: 20px;"></i>
                            <span class="small fw-semibold">Sign Out</span>
                        </a>
                        <form id="customer-logout-form" action="{{ route('customer.session.destroy') }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>

            {{-- Right Column: Dynamic Form Fields Workspace --}}
            <div class="col-lg-8 col-xl-9">

                <div id="ajaxAlertContainer"></div>

                @if (session()->has('success'))
                    <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
                        {{ session()->get('success') }}
                    </div>
                @endif

                <div class="card border-0 shadow-sm rounded-3 bg-white p-4 p-md-5">

                    <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
                        <div>
                            <h4 class="fw-bold text-dark m-0">Account Overview</h4>
                            <p class="text-muted small m-0 mt-1">Update your public profile configuration and primary contact settings.</p>
                        </div>
                    </div>

                    <form id="profileUpdateForm" action="{{ route('customer.profile.store') }}" method="POST" autocomplete="off">
                        @csrf
                        <div class="row g-4">

                            <div class="col-12 mt-2">
                                <h6 class="text-primary text-uppercase fw-bold tracking-wider m-0" style="font-size: 11px;">Personal Information</h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-secondary small fw-bold mb-2">First Name</label>
                                <input type="text" name="first_name" class="form-control custom-input py-25" value="{{ old('first_name') ?? $customer->first_name }}" placeholder="First name required">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-secondary small fw-bold mb-2">Last Name</label>
                                <input type="text" name="last_name" class="form-control custom-input py-25" value="{{ old('last_name') ?? $customer->last_name }}" placeholder="Last name required">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-secondary small fw-bold mb-2">Email Address</label>
                                <input type="email" name="email" class="form-control custom-input py-25" value="{{ old('email') ?? $customer->email }}" placeholder="name@domain.com">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-secondary small fw-bold mb-2">Phone Number</label>
                                <input type="tel" name="phone" class="form-control custom-input py-25" value="{{ old('phone') ?? $customer->phone }}" placeholder="Mobile layout configuration">
                            </div>

                            <div class="col-12 mt-4">
                                <hr class="text-muted opacity-25 mb-4">
                                <h6 class="text-primary text-uppercase fw-bold tracking-wider m-0" style="font-size: 11px;">Default Shipping Details</h6>
                            </div>

                            <div class="col-12">
                                <label class="form-label text-secondary small fw-bold mb-2">Primary Street Address</label>
                                <input type="text" name="address1" class="form-control custom-input py-25" value="{{ old('address1') ?? ($customer->default_address->address1 ?? '') }}" placeholder="Street, block number, apartment">
                            </div>

                            <div class="col-md-5">
                                <label class="form-label text-secondary small fw-bold mb-2">City / Town</label>
                                <input type="text" name="city" class="form-control custom-input py-25" value="{{ old('city') ?? ($customer->default_address->city ?? '') }}" placeholder="City location">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-secondary small fw-bold mb-2">Region / County</label>
                                <input type="text" name="state" class="form-control custom-input py-25" value="{{ old('state') ?? ($customer->default_address->state ?? '') }}" placeholder="State code or province">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label text-secondary small fw-bold mb-2">Postal Code</label>
                                <input type="text" name="postcode" class="form-control custom-input py-25" value="{{ old('postcode') ?? ($customer->default_address->postcode ?? '') }}" placeholder="Zip code structure">
                            </div>

                        </div>

                        <div class="d-flex align-items-center justify-content-end gap-3 border-top pt-4 mt-5">
                            <button type="button" class="btn btn-light rounded-pill px-4 py-2 fw-semibold text-secondary small" onclick="window.location.reload();">
                                Discard
                            </button>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold small shadow-sm">
                                Save Changes
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

@include('partials.footer')

<style>
    .py-25 { padding-top: 0.65rem !important; padding-bottom: 0.65rem !important; }
    .tracking-wider { letter-spacing: 1px; }
    .custom-input {
        background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; color: #333333; font-size: 0.9rem;
        transition: all 0.2s cubic-bezier(0.165, 0.84, 0.44, 1);
    }
    .custom-input:focus { background-color: #ffffff; border-color: #ff6600; box-shadow: 0 0 0 4px rgba(255, 102, 0, 0.1); outline: none; }
    .profile-sidebar-nav .list-group-item { transition: all 0.2s ease-in-out; color: #555555; }
    .profile-sidebar-nav .list-group-item:hover { background-color: #f8f9fa; color: #ff6600; padding-left: 1.25rem; }
    .profile-sidebar-nav .list-group-item.active {
        background-color: rgba(255, 102, 0, 0.08) !important; border-left: 4px solid #ff6600 !important; color: #ff6600 !important; padding-left: 1.25rem;
    }
    .profile-sidebar-nav .list-group-item.active i { color: #ff6600 !important; }
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.12) !important; }
</style>

<script>
    $(document).ready(function() {
        $('#profileUpdateForm').on('submit', function() {
            var $submitBtn = $(this).find('button[type="submit"]');
            $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Saving...');
        });

        $('#avatarUploadBtn').on('click', function() {
            $('#avatarFileInput').click();
        });

        $('#avatarFileInput').on('change', function() {
            var file = this.files[0];
            if (!file) return;

            if (!file.type.match('image.*')) {
                renderAlert('danger', 'Please select a valid image file formatting type (PNG, JPEG).');
                return;
            }

            var formData = new FormData();
            formData.append('avatar', file);
            formData.append('_token', '{{ csrf_token() }}');

            $('#avatarSpinner').removeClass('d-none').addClass('d-flex');
            $('#avatarUploadBtn').prop('disabled', true);

            $.ajax({
                url: "{{ route('customer.profile.avatar') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if(response.success) {
                        var busterUrl = response.avatar_url + '?t=' + new Date().getTime();
                        $('#profileAvatarPreview').attr('src', busterUrl);
                        renderAlert('success', response.message);
                    }
                },
                error: function(xhr) {
                    var errorMsg = 'An error occurred during file delivery upload.';
                    if(xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    renderAlert('danger', errorMsg);
                },
                complete: function() {
                    $('#avatarSpinner').removeClass('d-flex').addClass('d-none');
                    $('#avatarUploadBtn').prop('disabled', false);
                    $('#avatarFileInput').val('');
                }
            });
        });

        function renderAlert(status, text) {
            var html = '<div class="alert alert-' + status + ' border-0 shadow-sm rounded-3 mb-4 alert-dismissible fade show" role="alert">' +
                text + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            $('#ajaxAlertContainer').html(html);
        }
    });
</script>
