@extends('partials.layouts.master')

@section('title', 'Item Sizes Master | Bhandari Packers')
@section('sub-title', 'Item Sizes Management')
@section('pagetitle', 'Master Settings')
@section('buttonTitle', '+ Add Size')
@section('buttonLink', route('admin.item-sizes.create'))

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-hover overflow-hidden">
                <div class="card-body">
                    <table id="item-sizes-table" class="table table-hover w-100">
                        <thead class="bg-light">
                            <tr>
                                <th>#</th>
                                <th>Size Name</th>
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
            var table = initDataTable('#item-sizes-table', '{{ route('admin.item-sizes') }}', [
                { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false },
                { data: 'size_name', name: 'size_name' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]);
        });
    </script>
@endsection
