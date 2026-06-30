@extends('partials.layouts.master')

@section('title', 'Role Management | Bhandari Packers')

@section('sub-title', 'Role Manage')
@section('pagetitle', 'Roles')
@section('buttonTitle')
    <i class="ri-add-line me-1"></i>Add Role
@endsection
@section('buttonLink', route('role.create'))

@section('content')

    <div class="row g-4">
        <div class="col-12">
            <div class="card mb-0 h-100">
                <table id="role-table" class="table-hover align-middle table table-nowrap w-100">
                    <thead class="bg-light bg-opacity-30">
    <tr>
        <th>ID</th>
        <th>Role Name</th>
        <th>Action</th>
    </tr>
</thead>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = initDataTable('#role-table', '{{ route('role.index') }}', [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name'
                },

                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]);

            @if (session('success'))
                showToast('{{ session('success') }}');
            @endif
        });
    </script>
@endsection
