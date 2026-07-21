@extends('partials.layouts.master')
@section('title', 'Vehicle Management | ServiceHub')
@section('sub-title', 'Vehicles')
@section('pagetitle', 'Vehicle Management')
@section('buttonTitle', '+ Add Vehicle')
@section('buttonLink', route('admin.vehicles.create'))
@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card mb-0 h-100">
            <div class="card-body p-0">
                <table id="vehicle-table" class="table table-hover align-middle table-nowrap w-100">
                    <thead class="bg-light bg-opacity-30">
                        <tr>
                            <th>#</th>
                            <th>Vehicle Name</th>
                            <th>Capacity Score</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('partials.common-drawer')
@endsection
@section('js')
<script>
    $(document).ready(function() {
        initDataTable('#vehicle-table', '{{ route('admin.vehicles') }}', [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'vehicle_name', name: 'vehicle_name' },
            { data: 'vehicle_capacity_score', name: 'vehicle_capacity_score' },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]);
        @if(session('success')) showToast('{{ session('success') }}'); @endif
    });
</script>
@endsection
