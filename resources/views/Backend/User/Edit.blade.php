@extends('partials.layouts.master')

@section('title')
    Edit {{ $user->name }} | Bhandari Packers
@endsection

@section('sub-title', 'Edit User')
@section('pagetitle', 'Dashboard')

@section('content')

    <div class="row g-4">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('user.index') }}">Users</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit User</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">
            <div class="card h-100 mb-0">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit User</h5>
                </div>
                <div class="card-body">
                    @include('Backend.User.Partials.UserFormContent', [
                        'user' => $user,
                        'roles' => $roles,
                        'supervisors' => $supervisors,
                        'assignedSupervisors' => $userSupervisors,
                        'formAction' => route('user.update', $user->id),
                        'formMethod' => 'PUT'
                    ])
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
