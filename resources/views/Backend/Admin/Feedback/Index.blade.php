@extends('partials.layouts.master')
@section('title', 'Feedback & Ratings | ServiceHub')
@section('sub-title', 'Feedback')
@section('pagetitle', 'Feedback & Ratings')
@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card mb-0 shadow-sm border-0 h-100" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-white py-3 border-bottom border-light">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="card-title mb-0 fw-bold text-dark"><i class="ri-discuss-line text-danger me-2 fs-18"></i>Customer Feedback & Ratings List</h6>
                        <small class="text-muted">Real-time reviews collected from the website booking engine.</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="feedback-table" class="table table-hover align-middle w-100 mb-0">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th class="py-3 px-4" style="font-weight: 700; width: 60px;">#</th>
                                <th class="py-3" style="font-weight: 700;">Booking No.</th>
                                <th class="py-3" style="font-weight: 700;">Customer</th>
                                <th class="py-3" style="font-weight: 700; width: 140px;">Rating</th>
                                <th class="py-3" style="font-weight: 700; width: 110px;">Sentiment</th>
                                <th class="py-3" style="font-weight: 700; min-width: 250px;">Review Comment</th>
                                <th class="py-3" style="font-weight: 700; width: 130px;">Date Submitted</th>
                                <th class="py-3 px-4 text-end" style="font-weight: 700; width: 90px;">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
    $(document).ready(function() {
        initDataTable('#feedback-table', '{{ route('admin.feedback') }}', [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'px-4 text-muted fw-semibold' },
            { data: 'booking_no', name: 'booking.booking_number', className: 'fw-bold text-primary' },
            { data: 'customer_name', name: 'customer.name', className: 'fw-semibold text-dark' },
            { data: 'rating_stars', name: 'rating', orderable: false, searchable: false },
            { data: 'label', name: 'label', orderable: false, searchable: false },
            { data: 'review', name: 'review', className: 'text-wrap text-muted small' },
            { data: 'created_at', name: 'created_at', className: 'text-nowrap' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'px-4 text-end' }
        ]);
        @if(session('success')) showToast('{{ session('success') }}'); @endif
    });
</script>
<style>
    #feedback-table { border-collapse: collapse !important; }
    #feedback-table th { font-size: 0.82rem; letter-spacing: 0.5px; text-transform: uppercase; border-bottom: 2px solid #f1f2f5; }
    #feedback-table td { font-size: 0.88rem; padding: 14px 10px; border-bottom: 1px solid #f1f2f5; }
    #feedback-table tbody tr:hover { background-color: #fafbfd; }
    .badge { font-weight: 600; padding: 5px 10px; border-radius: 4px; font-size: 0.75rem; letter-spacing: 0.3px; }
    .bg-success { background-color: #d1e7dd !important; color: #0f5132 !important; border: 1px solid #badbcc; }
    .bg-warning { background-color: #fff3cd !important; color: #664d03 !important; border: 1px solid #ffecb5; }
    .bg-danger { background-color: #f8d7da !important; color: #842029 !important; border: 1px solid #f5c2c7; }
</style>
@endsection
