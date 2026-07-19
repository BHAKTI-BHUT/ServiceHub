@extends('partials.layouts.master3')

@section('title', 'Supervisor Dashboard | Bhandari Packers')
@section('sub-title', 'Supervisor Dashboard Details')
@section('pagetitle', 'Dashboard')

@section('content')

    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary bg-opacity-10 border-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="text-primary fw-bold mb-1">Welcome Back, {{ auth()->user()->name }}! 👋</h4>
                            <p class="text-muted mb-0">Here is an overview of your shifting operations and booking tasks.</p>
                        </div>
                        <div>
                            <span class="badge bg-primary text-white px-3 py-2 fs-12">Supervisor Account</span>
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
                        <span class="fs-12 text-muted d-block mb-1">Total Assignments</span>
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
                        <h6 class="mb-0 fw-semibold fs-11 text-info">Active Shifting:</h6>
                        <span class="fs-12 text-info">{{ number_format($stats['active_bookings']) }}</span>
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
                        <span class="fs-12 text-muted d-block mb-1">New Assignments</span>
                        <h4 class="fw-semibold mb-0">{{ number_format($stats['pending_requests']) }}</h4>
                    </div>
                </div>
                <div class="card-body bg-light py-2 bg-opacity-40 hstack justify-content-between gap-3 border-top">
                    <span class="fs-12 text-muted">Awaiting acceptance</span>
                    <a href="{{ route('supervisor.booking.index') }}" class="btn btn-link p-0 fs-12 text-warning fw-semibold">View Requests</a>
                </div>
            </div>
        </div>

        <!-- Accepted Assignments Card -->
        <div class="col-lg-3 col-md-6">
            <div class="card card-hover overflow-hidden h-100">
                <div class="card-body hstack gap-3">
                    <div class="avatar avatar-item rounded-2 bg-success bg-opacity-10 text-success p-2 fs-24">
                        <i class="ri-checkbox-circle-line"></i>
                    </div>
                    <div>
                        <span class="fs-12 text-muted d-block mb-1">Accepted Tasks</span>
                        <h4 class="fw-semibold mb-0">{{ number_format($stats['accepted_bookings']) }}</h4>
                    </div>
                </div>
                <div class="card-body bg-light py-2 bg-opacity-40 hstack justify-content-between gap-3 border-top">
                    <span class="fs-12 text-muted">Ready or in progress</span>
                </div>
            </div>
        </div>

        <!-- Active Shifting Card -->
        <div class="col-lg-3 col-md-6">
            <div class="card card-hover overflow-hidden h-100">
                <div class="card-body hstack gap-3">
                    <div class="avatar avatar-item rounded-2 bg-info bg-opacity-10 text-info p-2 fs-24">
                        <i class="ri-roadster-line"></i>
                    </div>
                    <div>
                        <span class="fs-12 text-muted d-block mb-1">Active Shifting</span>
                        <h4 class="fw-semibold mb-0">{{ number_format($stats['active_bookings']) }}</h4>
                    </div>
                </div>
                <div class="card-body bg-light py-2 bg-opacity-40 hstack justify-content-between gap-3 border-top">
                    <span class="fs-12 text-muted">Shifting process started</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links & Actions -->
    <div class="row g-4 mt-2">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="card-title fw-semibold mb-0">Operations Quick Link</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-3">
                        <a href="{{ route('supervisor.booking.index') }}" class="btn btn-primary d-flex align-items-center justify-content-between p-3 rounded-2 text-start">
                            <div>
                                <h6 class="text-white fw-semibold mb-0">My Shifting Bookings</h6>
                                <span class="fs-11 text-white-50">Accept assignments, input OTP, upload proofs, complete shifting</span>
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
                    <h5 class="card-title fw-semibold mb-0">Operational Guidelines</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush fs-13">
                        <li class="list-group-item px-0 py-2 border-0"><i class="ri-checkbox-circle-fill text-success me-2"></i><strong>Accept Job:</strong> Acknowledge the assignment immediately when requested by the vendor.</li>
                        <li class="list-group-item px-0 py-2 border-0"><i class="ri-checkbox-circle-fill text-success me-2"></i><strong>Start Trip:</strong> Start the trip to go to the customer's pickup location.</li>
                        <li class="list-group-item px-0 py-2 border-0"><i class="ri-checkbox-circle-fill text-success me-2"></i><strong>OTP Check:</strong> Verify OTP with customer before starting the packing/pickup.</li>
                        <li class="list-group-item px-0 py-2 border-0"><i class="ri-checkbox-circle-fill text-success me-2"></i><strong>Upload Proofs:</strong> Take and upload photo proofs of packed boxes.</li>
                        <li class="list-group-item px-0 py-2 border-0"><i class="ri-checkbox-circle-fill text-success me-2"></i><strong>Collect Cash:</strong> If payment is cash, collect from customer and complete the job in-app.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

@endsection
