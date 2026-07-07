@extends('partials.layouts.master')
@section('title', 'Revenue | ServiceHub')
@section('sub-title', 'Revenue')
@section('pagetitle', 'Revenue Overview')
@section('content')
<div class="row g-4">
    <!-- Summary cards -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-hover overflow-hidden mb-0">
            <div class="card-body hstack gap-3 p-3">
                <div class="avatar avatar-item rounded-3 bg-success bg-opacity-10 text-success fs-20" style="width:45px;height:45px;display:flex;align-items:center;justify-content:center;">
                    <i class="ri-money-dollar-circle-fill"></i>
                </div>
                <div>
                    <span class="fs-12 text-muted d-block mb-1">Total Revenue</span>
                    <h4 class="fw-semibold mb-0 text-success">₹{{ number_format($totalRevenue, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-hover overflow-hidden mb-0">
            <div class="card-body hstack gap-3 p-3">
                <div class="avatar avatar-item rounded-3 bg-primary bg-opacity-10 text-primary fs-20" style="width:45px;height:45px;display:flex;align-items:center;justify-content:center;">
                    <i class="ri-wallet-3-fill"></i>
                </div>
                <div>
                    <span class="fs-12 text-muted d-block mb-1">Registration Collected</span>
                    <h4 class="fw-semibold mb-0">₹{{ number_format($registrationCollected, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-hover overflow-hidden mb-0">
            <div class="card-body hstack gap-3 p-3">
                <div class="avatar avatar-item rounded-3 bg-info bg-opacity-10 text-info fs-20" style="width:45px;height:45px;display:flex;align-items:center;justify-content:center;">
                    <i class="ri-bank-card-fill"></i>
                </div>
                <div>
                    <span class="fs-12 text-muted d-block mb-1">Remaining Collected</span>
                    <h4 class="fw-semibold mb-0">₹{{ number_format($remainingCollected, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-hover overflow-hidden mb-0">
            <div class="card-body hstack gap-3 p-3">
                <div class="avatar avatar-item rounded-3 bg-warning bg-opacity-10 text-warning fs-20" style="width:45px;height:45px;display:flex;align-items:center;justify-content:center;">
                    <i class="ri-time-fill"></i>
                </div>
                <div>
                    <span class="fs-12 text-muted d-block mb-1">Pending Revenue</span>
                    <h4 class="fw-semibold mb-0">₹{{ number_format($pendingRevenue, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Table -->
    <div class="col-12">
        <div class="card mb-0 h-100">
            <div class="card-body p-0">
                <table id="revenue-table" class="table table-hover align-middle table-nowrap w-100">
                    <thead class="bg-light bg-opacity-30">
                        <tr>
                            <th>#</th>
                            <th>Booking No.</th>
                            <th>Customer</th>
                            <th>Total Amount (₹)</th>
                            <th>Reg. Fee (₹)</th>
                            <th>Remaining (₹)</th>
                            <th>Payment Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
    $(document).ready(function() {
        initDataTable('#revenue-table', '{{ route('admin.revenue') }}', [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'booking_number', name: 'booking_number' },
            { data: 'customer_name', name: 'customer.name' },
            { data: 'amount', name: 'amount' },
            { data: 'registration_charge', name: 'registration_charge' },
            { data: 'remaining_amount', name: 'remaining_amount' },
            { data: 'payment_status_badge', name: 'payment_status', orderable: false, searchable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]);
        @if(session('success')) showToast('{{ session('success') }}'); @endif
    });
</script>
@endsection
