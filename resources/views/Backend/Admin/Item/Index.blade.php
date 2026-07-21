@extends('partials.layouts.master')
@section('title', 'Item Master | ServiceHub')
@section('sub-title', 'Item Master')
@section('pagetitle', 'Item Master')
@section('buttonTitle', '+ Add Item')
@section('buttonLink', route('admin.items.create'))
@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card mb-0 h-100">
            <div class="card-body p-0">
                <table id="item-table" class="table table-hover align-middle table-nowrap w-100">
                    <thead class="bg-light bg-opacity-30">
                        <tr>
                            <th>#</th>
                            <th>Item Name</th>
                            <th>Item Size</th>
                            <th>Score Point</th>
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
        initDataTable('#item-table', '{{ route('admin.items') }}', [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'item_name', name: 'item_name' },
            { data: 'size_name', name: 'size.size_name' },
            { data: 'score_point', name: 'score_point' },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]);
        @if(session('success')) showToast('{{ session('success') }}'); @endif
    });
</script>
@endsection
