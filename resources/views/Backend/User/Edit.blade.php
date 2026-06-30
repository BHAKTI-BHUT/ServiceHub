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
                    <div id="drawer-form-content">
                        <form id="editUserForm" action="{{ route('user.update', $user->id) }}" method="POST"
                            class="needs-validation" novalidate>
                            @csrf
                            @method('PUT')
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Full Name" value="{{ $user->name }}" required>
                                    <div class="invalid-feedback">Please enter a name.</div>
                                </div>
                                <div class="col-md-12">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="Email" value="{{ $user->email }}" required>
                                    <div class="invalid-feedback">Please provide a valid email.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="mobile" class="form-label">Mobile Number</label>
                                    <input type="text" class="form-control" id="mobile" name="mobile"
                                        placeholder="Mobile Number" value="{{ $user->mobile }}">
                                    <div class="invalid-feedback">Please enter a valid mobile number.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a status.</div>
                                </div>
                                <div class="col-md-12">
                                    <label for="password" class="form-label">Password (Leave blank to keep current)</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="New Password" minlength="6">
                                    <div class="invalid-feedback">Password must be at least 6 characters when changed.</div>
                                </div>
                                <div class="col-md-12">
                                    <label for="roles" class="form-label">Roles</label>
                                    <select class="form-select select2" id="roles" name="roles[]" multiple required>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}"
                                                {{ in_array($role->name, $userRoles) ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Please select at least one role.</div>
                                </div>
                                <div class="col-md-12 {{ in_array('Vendor', $userRoles) ? '' : 'd-none' }}" id="supervisors-group">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="supervisors" class="form-label mb-0">Assign Supervisors</label>
                                        <button type="button" class="btn btn-sm btn-link text-primary p-0" data-bs-toggle="modal" data-bs-target="#quickCreateSupervisorModal">
                                            <i class="ri-add-line align-bottom"></i> Create New Supervisor
                                        </button>
                                    </div>
                                    <select class="form-select select2" id="supervisors" name="supervisors[]" multiple>
                                        @foreach ($supervisors as $supervisor)
                                            <option value="{{ $supervisor->id }}"
                                                {{ in_array($supervisor->id, $userSupervisors) ? 'selected' : '' }}>
                                                {{ $supervisor->name }} ({{ $supervisor->mobile ?? 'No Mobile' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Please select at least one supervisor.</div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('Backend.User.Partials.QuickCreateSupervisorModal')

@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                width: '100%'
            });

            // Toggle supervisors field based on role selection
            function toggleSupervisors() {
                var selectedRoles = $('#roles').val() || [];
                if (selectedRoles.includes('Vendor')) {
                    $('#supervisors-group').removeClass('d-none');
                } else {
                    $('#supervisors-group').addClass('d-none');
                    $('#supervisors').val(null).trigger('change');
                }
            }

            $('#roles').on('change', toggleSupervisors);

            // Form Submission
            $('#editUserForm').on('submit', function(e) {
                e.preventDefault();

                // reset previous errors
                $('#editUserForm .is-invalid').removeClass('is-invalid');

                if (this.checkValidity()) {
                    $.ajax({
                        url: '{{ route('user.update', $user->id) }}',
                        method: 'PUT',
                        data: $(this).serialize(),
                        success: function(response) {
                            showToast(response.message);
                            setTimeout(function() {
                                window.location.href = '{{ route('user.index') }}';
                            }, 1000);
                        },
                        error: function(xhr) {
                            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON
                                .errors) {
                                var errors = xhr.responseJSON.errors;
                                Object.keys(errors).forEach(function(key) {
                                    var field = $('#editUserForm').find('[name="' +
                                        key + '"]');
                                    if (field.length) {
                                        field.addClass('is-invalid');
                                        field.closest('.col-md-6, .mb-3').find(
                                            '.invalid-feedback').text(errors[key][
                                            0
                                        ]);
                                    }
                                });
                            } else {
                                showToast('An error occurred.', 'danger');
                            }
                        }
                    });
                }
                $(this).addClass('was-validated');
            });
        });
    </script>
@endsection
