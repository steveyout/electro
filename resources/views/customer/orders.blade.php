@include('partials.header')

<div class="container-fluid py-5 bg-light">
    <div class="container">
        <h2 class="mb-4 fw-bold">My <span class="text-primary">Orders</span></h2>

        <div class="row g-4">
            {{-- Left Side: Orders Table --}}
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="table-responsive p-3">
                        <table class="table align-middle">
                            <thead class="text-muted small uppercase">
                            <tr>
                                <th class="border-0 ps-3">Order ID</th>
                                <th class="border-0">Date</th>
                                <th class="border-0">Status</th>
                                <th class="border-0">Total</th>
                                <th class="border-0 text-end pe-3">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($orders && $orders->count() > 0)
                                @foreach($orders as $order)
                                    <tr>
                                        <td class="py-3 ps-3">
                                            <span class="fw-bold text-dark">#{{ $order->increment_id }}</span>
                                        </td>
                                        <td class="text-muted">
                                            {{ $order->created_at->format('d M, Y') }}
                                        </td>
                                        <td>
                                            @php
                                                $statusColor = match($order->status) {
                                                    'completed' => 'bg-success',
                                                    'pending'   => 'bg-warning text-dark',
                                                    'processing'=> 'bg-info text-white',
                                                    'canceled'  => 'bg-danger',
                                                    default     => 'bg-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $statusColor }} rounded-pill px-3 py-2 small text-capitalize">
                                                    {{ $order->status }}
                                                </span>
                                        </td>
                                        <td class="fw-bold text-primary">
                                            {{ core()->formatPrice($order->grand_total, $order->order_currency_code) }}
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('customer.orders.view', $order->id) }}"
                                               class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold">
                                                View Details
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="py-4">
                                            <i class="fas fa-box-open fa-4x text-light mb-3"></i>
                                            <p class="text-muted fs-5">You haven't placed any orders yet.</p>
                                            <a href="{{ route('shop.home.index') }}" class="btn btn-primary rounded-pill px-5 py-2 mt-2 shadow-sm">
                                                Start Shopping
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pagination using same style as card --}}
                @if($orders->count() > 0 && method_exists($orders, 'links'))
                    <div class="mt-4 d-flex justify-content-center">
                        {!! $orders->links() !!}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@include('partials.footer')

<style>
    /* Table Styling to match Cart */
    .table thead th {
        letter-spacing: 1px;
        font-weight: 600;
        background-color: #f8f9fa;
        padding: 15px 10px;
    }

    .table tbody tr {
        border-bottom: 1px solid #eee;
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: #fafafa;
    }

    .table tbody tr:last-child {
        border-bottom: none;
    }

    /* Status Badge Overrides */
    .badge {
        font-weight: 500;
        font-size: 11px;
    }

    /* Adjust Primary Color to match your specific branding if needed */
    .text-primary {
        color: #ff6600 !important; /* Example: matching the orange you use */
    }
    .btn-primary {
        background-color: #ff6600 !important;
        border-color: #ff6600 !important;
    }
    .btn-outline-primary {
        color: #ff6600 !important;
        border-color: #ff6600 !important;
    }
    .btn-outline-primary:hover {
        background-color: #ff6600 !important;
        color: #fff !important;
    }
</style>
