@extends('partials.layouts.master')

@section('title', 'Add New User | Bhandari Packers')

@section('sub-title', 'Add User')
@section('pagetitle', 'Dashboard')

@section('content')

    <div class="row g-4">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('user.index') }}">Users</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add User</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">
            <div class="card h-100 mb-0">
                <div class="card-header">
                    <h5 class="card-title mb-0">Add New User</h5>
                </div>
                <div class="card-body">
                    @include('Backend.User.Partials.UserFormContent', ['user' => null, 'roles' => $roles, 'supervisors' => $supervisors, 'assignedSupervisors' => [], 'formAction' => route('user.store'), 'formMethod' => 'POST'])
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        $(document).ready(function() {
            @if (session('success'))
                showToast('{{ session('success') }}');
            @endif
        });
    </script>
@endsection
