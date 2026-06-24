@extends('partials.layouts.master')
@section('title', 'Reports | ServiceHub')
@section('sub-title', 'Reports')
@section('pagetitle', 'Reports')
@section('buttonTitle', 'Export CSV')
@section('buttonLink', '#')
@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card mb-0 h-100">
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">From Date</label>
                        <input type="date" class="form-control" id="fromDate">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">To Date</label>
                        <input type="date" class="form-control" id="toDate">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="statusFilter">
                            <option value="">All</option>
                            <option value="order_confirmed">Confirmed</option>
                            <option value="shifting_completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary w-100" id="filterBtn">
                            <i class="ri-filter-line me-1"></i> Filter
                        </button>
                    </div>
                </div>
                <table id="report-table" class="table table-hover align-middle table-nowrap w-100">
                    <thead class="bg-light bg-opacity-30">
                        <tr>
                            <th>#</th>
                            <th>Booking No.</th>
                            <th>Customer</th>
                            <th>Pickup</th>
                            <th>Drop</th>
                            <th>Date</th>
                            <th>Amount (₹)</th>
                            <th>Status</th>
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
        var table = initDataTable('#report-table', '{{ route('admin.reports') }}', [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'booking_number', name: 'booking_number' },
            { data: 'customer_name', name: 'customer.name' },
            { data: 'pickup_location', name: 'pickup_location' },
            { data: 'drop_location', name: 'drop_location' },
            { data: 'shifting_date', name: 'shifting_date' },
            { data: 'amount', name: 'amount' },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false }
        ]);

        $('#filterBtn').on('click', function() {
            var url = '{{ route('admin.reports') }}?from=' + $('#fromDate').val() + '&to=' + $('#toDate').val() + '&status=' + $('#statusFilter').val();
            table.ajax.url(url).load();
        });

        @if(session('success')) showToast('{{ session('success') }}'); @endif
    });
</script>
@endsection
