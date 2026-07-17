@extends('partials.layouts.master3')

@section('title', 'Vendor Dashboard | Bhandari Packers')
@section('sub-title', 'Vendor Dashboard Details')
@section('pagetitle', 'Dashboard')

@section('content')

    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary bg-opacity-10 border-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="text-primary fw-bold mb-1">Welcome Back, {{ auth()->user()->name }}! 👋</h4>
                            <p class="text-muted mb-0">Here is an overview of your bookings, earnings, and operations.</p>
                        </div>
                        <div>
                            <span class="badge bg-primary text-white px-3 py-2 fs-12">Vendor Account</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Total Bookings Card -->
        <div class="col-lg-3 col-md-6">
            <div class="card card-hover overflow-hidden h-100">
                <div class="card-body hstack gap-3">
                    <div class="avatar avatar-item rounded-2 bg-primary bg-opacity-10 text-primary p-2 fs-24">
                        <i class="ri-calendar-todo-line"></i>
                    </div>
                    <div>
                        <span class="fs-12 text-muted d-block mb-1">Total Bookings</span>
                        <h4 class="fw-semibold mb-0">{{ number_format($stats['total_bookings']) }}</h4>
                    </div>
                </div>
                <div class="card-body bg-light py-2 bg-opacity-40 hstack justify-content-between gap-3 border-top">
                    <div class="hstack gap-2">
                        <h6 class="mb-0 fw-semibold fs-11 text-success">Completed:</h6>
                        <span class="fs-12 text-success">{{ number_format($stats['completed_bookings']) }}</span>
                    </div>
                    <div class="vr bg-light"></div>
                    <div class="hstack gap-2">
                        <h6 class="mb-0 fw-semibold fs-11 text-warning">Pending:</h6>
                        <span class="fs-12 text-warning">{{ number_format($stats['pending_bookings']) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Requests Card -->
        <div class="col-lg-3 col-md-6">
            <div class="card card-hover overflow-hidden h-100">
                <div class="card-body hstack gap-3">
                    <div class="avatar avatar-item rounded-2 bg-warning bg-opacity-10 text-warning p-2 fs-24">
                        <i class="ri-notification-badge-line"></i>
                    </div>
                    <div>
                        <span class="fs-12 text-muted d-block mb-1">New Requests</span>
                        <h4 class="fw-semibold mb-0">{{ number_format($stats['pending_requests']) }}</h4>
                    </div>
                </div>
                <div class="card-body bg-light py-2 bg-opacity-40 hstack justify-content-between gap-3 border-top">
                    <span class="fs-12 text-muted">Awaiting your response</span>
                    <a href="{{ route('vendor.booking.index') }}" class="btn btn-link p-0 fs-12 text-warning fw-semibold">View Requests</a>
                </div>
            </div>
        </div>

        <!-- Total Earnings Card -->
        <div class="col-lg-3 col-md-6">
            <div class="card card-hover overflow-hidden h-100">
                <div class="card-body hstack gap-3">
                    <div class="avatar avatar-item rounded-2 bg-success bg-opacity-10 text-success p-2 fs-24">
                        <i class="ri-money-dollar-circle-line"></i>
                    </div>
                    <div>
                        <span class="fs-12 text-muted d-block mb-1">Total Earnings</span>
                        <h4 class="fw-semibold mb-0">₹{{ number_format($stats['total_revenue'], 2) }}</h4>
                    </div>
                </div>
                <div class="card-body bg-light py-2 bg-opacity-40 hstack justify-content-between gap-3 border-top">
                    <span class="fs-12 text-muted">Settled commission bookings</span>
                    <a href="{{ route('vendor.wallet.index') }}" class="btn btn-link p-0 fs-12 text-success fw-semibold">Wallet</a>
                </div>
            </div>
        </div>

        <!-- Pending Revenue Card -->
        <div class="col-lg-3 col-md-6">
            <div class="card card-hover overflow-hidden h-100">
                <div class="card-body hstack gap-3">
                    <div class="avatar avatar-item rounded-2 bg-info bg-opacity-10 text-info p-2 fs-24">
                        <i class="ri-time-line"></i>
                    </div>
                    <div>
                        <span class="fs-12 text-muted d-block mb-1">Awaiting Payment</span>
                        <h4 class="fw-semibold mb-0">₹{{ number_format($stats['pending_revenue'], 2) }}</h4>
                    </div>
                </div>
                <div class="card-body bg-light py-2 bg-opacity-40 hstack justify-content-between gap-3 border-top">
                    <span class="fs-12 text-muted">From active trips/pending fees</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links & Actions -->
    <div class="row g-4 mt-2">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title fw-semibold mb-0">Quick Operations</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-3">
                        <a href="{{ route('vendor.booking.index') }}" class="btn btn-primary d-flex align-items-center justify-content-between p-3 rounded-2 text-start">
                            <div>
                                <h6 class="text-white fw-semibold mb-0">Manage Bookings</h6>
                                <span class="fs-11 text-white-50">Respond to booking requests, assign supervisors</span>
                            </div>
                            <i class="ri-arrow-right-line fs-18"></i>
                        </a>
                        <a href="{{ route('vendor.wallet.index') }}" class="btn btn-success d-flex align-items-center justify-content-between p-3 rounded-2 text-start">
                            <div>
                                <h6 class="text-white fw-semibold mb-0">Wallet & Settlements</h6>
                                <span class="fs-11 text-white-50">View payout transactions, balance & settlement statements</span>
                            </div>
                            <i class="ri-arrow-right-line fs-18"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title fw-semibold mb-0">Status Summary</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <tbody>
                                <tr>
                                    <td><span class="badge bg-warning-focus text-warning px-2 py-1 fs-11">Pending</span></td>
                                    <td>Bookings awaiting supervisor assignment or start</td>
                                    <td class="text-end fw-semibold">{{ number_format($stats['pending_bookings']) }}</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-primary-focus text-primary px-2 py-1 fs-11">Confirmed</span></td>
                                    <td>Bookings accepted and confirmed by you</td>
                                    <td class="text-end fw-semibold">{{ number_format($stats['confirmed_bookings']) }}</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-success-focus text-success px-2 py-1 fs-11">Completed</span></td>
                                    <td>Successfully closed bookings</td>
                                    <td class="text-end fw-semibold">{{ number_format($stats['completed_bookings']) }}</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-danger-focus text-danger px-2 py-1 fs-11">Cancelled</span></td>
                                    <td>Cancelled bookings</td>
                                    <td class="text-end fw-semibold">{{ number_format($stats['cancelled_bookings']) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
