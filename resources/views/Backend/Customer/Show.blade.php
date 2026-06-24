@extends('partials.layouts.master')

@section('title')
    {{ $customer->name }} Details | Herozi
@endsection

@section('sub-title', 'Customer Profile')
@section('pagetitle', 'Customers')

@section('content')

    <div class="row g-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('customer.index') }}">Customers</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $customer->name }} Details</li>
                </ol>
            </nav>
        </div>

        <!-- Left Profile Details Column -->
        <div class="col-lg-4">
            <div class="card h-100 mb-0">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <img src="{{ $customer->image ? asset($customer->image) : asset('assets/images/avatar/dummy-avatar.jpg') }}"
                            alt="Avatar" class="rounded-circle border p-1" width="120" height="120"
                            style="object-fit: cover;">
                    </div>
                    <h5 class="mb-1">{{ $customer->name }}</h5>
                    <p class="text-muted mb-3">Customer ID: #{{ $customer->id }}</p>
                    <div class="mb-3">
                        @if ($customer->status === 'active')
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </div>
                    <hr class="my-4">
                    <div class="text-start">
                        <h6 class="text-uppercase fs-11 text-muted mb-3">Personal Details</h6>
                        <div class="d-flex flex-column gap-3 fs-13">
                            <div>
                                <span class="text-muted d-block">Email Address</span>
                                <span class="fw-medium text-break">{{ $customer->email }}</span>
                            </div>
                            <div>
                                <span class="text-muted d-block">Mobile Number</span>
                                <span class="fw-medium">{{ $customer->mobile ?? '—' }}</span>
                            </div>
                            <div>
                                <span class="text-muted d-block">City</span>
                                <span class="fw-medium">{{ $customer->city ?? '—' }}</span>
                            </div>
                            <div>
                                <span class="text-muted d-block">Address</span>
                                <span class="fw-medium">{{ $customer->address ?? '—' }}</span>
                            </div>
                            <div>
                                <span class="text-muted d-block">Registered On</span>
                                <span class="fw-medium">{{ $customer->created_at ? $customer->created_at->format('d M Y, h:i A') : '—' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Booking History Column -->
        <div class="col-lg-8">
            <div class="card h-100 mb-0">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Booking History</h5>
                    <span class="badge bg-primary-focus text-primary">{{ $bookings->count() }} Bookings</span>
                </div>
                <div class="card-body p-0">
                    @if ($bookings->isEmpty())
                        <div class="text-center p-5">
                            <div class="avatar avatar-lg rounded-circle bg-light text-muted mx-auto mb-3" style="width:60px; height:60px; display:flex; align-items:center; justify-content:center;">
                                <i class="ri-calendar-event-line fs-24"></i>
                            </div>
                            <h5>No bookings found</h5>
                            <p class="text-muted">This customer hasn't placed any bookings yet.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0">
                                <thead class="table-light table-nowrap bg-opacity-30">
                                    <tr>
                                        <th>Booking No</th>
                                        <th>Pickup & Drop</th>
                                        <th>Shifting Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bookings as $booking)
                                        <tr>
                                            <td>
                                                <a href="{{ route('booking.show', $booking->id) }}" class="font-monospace fw-semibold text-primary">
                                                    {{ $booking->booking_number }}
                                                </a>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 250px;">
                                                    <span class="text-success"><i class="ri-map-pin-user-line me-1"></i></span>{{ $booking->pickup_location }}
                                                </div>
                                                <div class="text-truncate mt-1" style="max-width: 250px;">
                                                    <span class="text-danger"><i class="ri-map-pin-5-line me-1"></i></span>{{ $booking->drop_location }}
                                                </div>
                                            </td>
                                            <td>
                                                <div>{{ date('d M Y', strtotime($booking->shifting_date)) }}</div>
                                                <span class="text-muted fs-11">{{ date('h:i A', strtotime($booking->shifting_time)) }}</span>
                                            </td>
                                            <td>₹{{ number_format($booking->amount, 2) }}</td>
                                            <td>
                                                @php
                                                    switch ($booking->status) {
                                                        case 'pending': $badge = 'bg-warning-focus text-warning'; break;
                                                        case 'confirmed': $badge = 'bg-primary-focus text-primary'; break;
                                                        case 'in_progress': $badge = 'bg-info-focus text-info'; break;
                                                        case 'completed': $badge = 'bg-success-focus text-success'; break;
                                                        case 'cancelled': $badge = 'bg-danger-focus text-danger'; break;
                                                        default: $badge = 'bg-light text-dark';
                                                    }
                                                @endphp
                                                <span class="badge {{ $badge }}">{{ ucfirst($booking->status) }}</span>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('booking.show', $booking->id) }}" class="btn icon-btn-sm btn-light-info" data-bs-toggle="tooltip" title="View Booking Detail">
                                                    <i class="ri-eye-line"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
