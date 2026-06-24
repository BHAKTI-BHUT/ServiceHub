@extends('partials.layouts.master')
@section('title', 'Feedback & Ratings | ServiceHub')
@section('sub-title', 'Feedback')
@section('pagetitle', 'Feedback & Ratings')
@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card mb-0 h-100">
            <div class="card-body p-0">
                <table id="feedback-table" class="table table-hover align-middle table-nowrap w-100">
                    <thead class="bg-light bg-opacity-30">
                        <tr>
                            <th>#</th>
                            <th>Booking No.</th>
                            <th>Customer</th>
                            <th>Rating</th>
                            <th>Label</th>
                            <th>Review</th>
                            <th>Date</th>
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
        initDataTable('#feedback-table', '{{ route('admin.feedback') }}', [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'booking_no', name: 'booking.booking_number' },
            { data: 'customer_name', name: 'customer.name' },
            { data: 'rating_stars', name: 'rating', orderable: false, searchable: false },
            { data: 'label', name: 'label', orderable: false, searchable: false },
            { data: 'review', name: 'review' },
            { data: 'created_at', name: 'created_at' }
        ]);
        @if(session('success')) showToast('{{ session('success') }}'); @endif
    });
</script>
@endsection
