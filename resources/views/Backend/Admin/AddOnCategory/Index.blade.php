@extends('partials.layouts.master')

@section('title', 'Add-on Categories | Bhandari Packers')
@section('sub-title', 'Add-on Categories')
@section('pagetitle', 'Master Settings')
@section('buttonTitle', '+ Add Category')
@section('buttonLink', route('admin.addon-categories.create'))

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-hover overflow-hidden">
                <div class="card-body">
                    <table id="addon-category-table" class="table table-hover w-100">
                        <thead class="bg-light">
                            <tr>
                                <th>#</th>
                                <th>Category Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
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
            initDataTable('#addon-category-table', '{{ route('admin.addon-categories') }}', [
                { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'status_badge', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]);
        });
    </script>
@endsection
