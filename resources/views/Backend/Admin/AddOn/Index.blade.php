@extends('partials.layouts.master')
@section('title', 'Add-On Services | ServiceHub')
@section('sub-title', 'Add-On Services')
@section('pagetitle', 'Add-On Services')
@section('buttonTitle', '+ Add Service')
@section('buttonLink', route('admin.addons.create'))
@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card mb-0 h-100">
            <div class="card-body p-0">
                <table id="addon-table" class="table table-hover align-middle table-nowrap w-100">
                    <thead class="bg-light bg-opacity-30">
                        <tr>
                            <th>#</th>
                            <th>Category Name</th>
                            <th>Service Name</th>
                            <th>Price (₹)</th>
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
        initDataTable('#addon-table', '{{ route('admin.addons') }}', [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'category_name', name: 'category.name' },
            { data: 'addon_name', name: 'addon_name' },
            { data: 'price', name: 'price' },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]);
        @if(session('success')) showToast('{{ session('success') }}'); @endif
    });
</script>
@endsection
