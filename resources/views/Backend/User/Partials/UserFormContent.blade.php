{{-- Shared form partial — works inside both standalone page AND offcanvas drawer --}}
<div id="drawer-form-content">
    <form id="drawerForm" action="{{ $formAction }}" method="POST" class="needs-validation" novalidate>
        @csrf
        @if($formMethod === 'PUT')
            @method('PUT')
        @endif
        <div class="row g-4">
            <div class="col-md-12">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name"
                    placeholder="Full Name" required value="{{ $user->name ?? '' }}">
                <div class="invalid-feedback">Please enter a name.</div>
            </div>
            <div class="col-md-12">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email"
                    placeholder="Email" required value="{{ $user->email ?? '' }}">
                <div class="invalid-feedback">Please provide a valid email.</div>
            </div>
            <div class="col-md-6">
                <label for="mobile" class="form-label">Mobile Number</label>
                <input type="text" class="form-control" id="mobile" name="mobile"
                    placeholder="Mobile Number" value="{{ $user->mobile ?? '' }}">
                <div class="invalid-feedback">Please enter a valid mobile number.</div>
            </div>
            <div class="col-md-6">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="active" {{ (isset($user) && $user->status === 'inactive') ? '' : 'selected' }}>Active</option>
                    <option value="inactive" {{ (isset($user) && $user->status === 'inactive') ? 'selected' : '' }}>Inactive</option>
                </select>
                <div class="invalid-feedback">Please select a status.</div>
            </div>
            <div class="col-md-12">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password"
                    placeholder="{{ isset($user) ? 'Leave blank to keep current' : 'Password' }}" {{ isset($user) ? '' : 'required minlength=6' }}>
                <div class="invalid-feedback">Password must be at least 6 characters.</div>
            </div>
            <div class="col-md-12">
                <label for="roles" class="form-label">Roles</label>
                <select class="form-select select2" id="roles" name="roles[]" multiple required>
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}"
                            {{ (isset($user) && $user->hasRole($role->name)) ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                <div class="invalid-feedback">Please select at least one role.</div>
            </div>

            {{-- Assign Supervisors — shown only when Vendor role is selected --}}
            <div class="col-md-12 d-none" id="supervisors-group">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label for="supervisors" class="form-label mb-0">Assign Supervisors</label>
                    <button type="button" class="btn btn-sm btn-link text-primary p-0" id="openQuickSupervisorBtn">
                        <i class="ri-add-line align-bottom"></i> Create New Supervisor
                    </button>
                </div>
                <select class="form-select select2" id="supervisors" name="supervisors[]" multiple>
                    @foreach ($supervisors as $supervisor)
                        <option value="{{ $supervisor->id }}"
                            {{ in_array($supervisor->id, $assignedSupervisors ?? []) ? 'selected' : '' }}>
                            {{ $supervisor->name }} ({{ $supervisor->mobile ?? 'No Mobile' }})
                        </option>
                    @endforeach
                </select>
                <div class="invalid-feedback">Please select at least one supervisor.</div>
            </div>
        </div>
    </form>

    {{-- Quick Create Supervisor — inline collapsible section (avoids Bootstrap modal-inside-offcanvas issues) --}}
    <div class="card border border-primary mt-3 d-none" id="quickSupervisorCard">
        <div class="card-header bg-primary bg-opacity-10 py-2">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 text-primary"><i class="ri-user-add-line me-1"></i> Create & Assign New Supervisor</h6>
                <button type="button" class="btn-close btn-close-sm" id="closeQuickSupervisorBtn" aria-label="Close"></button>
            </div>
        </div>
        <div class="card-body">
            <form id="quickCreateSupervisorForm" novalidate>
                @csrf
                <div class="row g-3">
                    <div class="col-md-12">
                        <label for="quick_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="quick_name" name="name" required placeholder="Enter full name">
                        <div class="invalid-feedback">Please enter a name.</div>
                    </div>
                    <div class="col-md-6">
                        <label for="quick_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="quick_email" name="email" required placeholder="Enter email address">
                        <div class="invalid-feedback">Please enter a valid email.</div>
                    </div>
                    <div class="col-md-6">
                        <label for="quick_mobile" class="form-label">Mobile Number</label>
                        <input type="text" class="form-control" id="quick_mobile" name="mobile" placeholder="Enter mobile number">
                        <div class="invalid-feedback">Please enter a valid mobile number.</div>
                    </div>
                    <div class="col-md-12">
                        <label for="quick_password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="quick_password" name="password" required value="123456" placeholder="Enter password">
                            <button class="btn btn-outline-secondary" type="button" id="generate-password-btn">Generate</button>
                        </div>
                        <div class="invalid-feedback">Password must be at least 6 characters.</div>
                    </div>
                    <div class="col-12 text-end">
                        <button type="button" class="btn btn-light btn-sm me-1" id="cancelQuickSupervisorBtn">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="save-quick-supervisor-btn">
                            <i class="ri-save-line me-1"></i> Save Supervisor
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            // --- Role toggle: show/hide supervisor assignment ---
            function toggleSupervisors() {
                var selectedRoles = $('#roles').val() || [];
                var hasVendor = selectedRoles.some(function(role) {
                    return role.toLowerCase() === 'vendor';
                });

                if (hasVendor) {
                    $('#supervisors-group').removeClass('d-none');
                } else {
                    $('#supervisors-group').addClass('d-none');
                    $('#supervisors').val(null).trigger('change');
                }
            }

            // Initialize Select2 (with dropdown parent for offcanvas support)
            var $dropdownParent = $('#commonDrawer').length ? $('#commonDrawer') : $('body');

            $('#roles').select2({ dropdownParent: $dropdownParent, width: '100%' });
            $('#supervisors').select2({ dropdownParent: $dropdownParent, width: '100%' });

            $('#roles').on('change', toggleSupervisors);
            toggleSupervisors(); // run on load for edit pages

            // --- Quick Supervisor Card toggle ---
            $('#openQuickSupervisorBtn').on('click', function () {
                $('#quickSupervisorCard').removeClass('d-none').hide().slideDown(200);
            });
            $('#closeQuickSupervisorBtn, #cancelQuickSupervisorBtn').on('click', function () {
                $('#quickSupervisorCard').slideUp(200, function () {
                    $(this).addClass('d-none');
                });
            });

            // Generate random password
            $('#generate-password-btn').on('click', function () {
                var chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
                var pass = "";
                for (var i = 0; i < 10; i++) {
                    pass += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                $('#quick_password').val(pass);
            });

            // Quick create supervisor AJAX
            $('#quickCreateSupervisorForm').on('submit', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var form = $(this);
                form.find('.is-invalid').removeClass('is-invalid');

                if (this.checkValidity()) {
                    $('#save-quick-supervisor-btn').prop('disabled', true).html('<i class="ri-loader-4-line ri-spin me-1"></i> Saving...');
                    $.ajax({
                        url: '{{ route("user.quick-create-supervisor") }}',
                        method: 'POST',
                        data: form.serialize(),
                        success: function (response) {
                            showToast(response.message);

                            // Add option to Select2 and auto-select it
                            var supervisor = response.user;
                            var text = supervisor.name + ' (' + (supervisor.mobile || 'No Mobile') + ')';
                            var newOption = new Option(text, supervisor.id, true, true);
                            $('#supervisors').append(newOption).trigger('change');

                            // Reset form & close card
                            form[0].reset();
                            $('#quick_password').val('123456');
                            form.removeClass('was-validated');
                            $('#quickSupervisorCard').slideUp(200, function () {
                                $(this).addClass('d-none');
                            });
                            $('#save-quick-supervisor-btn').prop('disabled', false).html('<i class="ri-save-line me-1"></i> Save Supervisor');
                        },
                        error: function (xhr) {
                            $('#save-quick-supervisor-btn').prop('disabled', false).html('<i class="ri-save-line me-1"></i> Save Supervisor');
                            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                                var errors = xhr.responseJSON.errors;
                                Object.keys(errors).forEach(function (key) {
                                    var field = form.find('[name="' + key + '"]');
                                    if (field.length) {
                                        field.addClass('is-invalid');
                                        field.closest('.col-md-12, .col-md-6').find('.invalid-feedback').text(errors[key][0]);
                                    }
                                });
                            } else {
                                showToast('An error occurred while creating supervisor.', 'danger');
                            }
                        }
                    });
                }
                form.addClass('was-validated');
            });

            // Main form submission via AJAX
            $('#drawerForm').on('submit', function (e) {
                e.preventDefault();
                var form = $(this);
                form.find('.is-invalid').removeClass('is-invalid');

                if (this.checkValidity()) {
                    $.ajax({
                        url: form.attr('action'),
                        method: 'POST',
                        data: form.serialize(),
                        success: function (response) {
                            showToast(response.message);
                            setTimeout(function () {
                                window.location.href = '{{ route("user.index") }}';
                            }, 1000);
                        },
                        error: function (xhr) {
                            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                                var errors = xhr.responseJSON.errors;
                                Object.keys(errors).forEach(function (key) {
                                    var field = form.find('[name="' + key + '"]');
                                    if (field.length) {
                                        field.addClass('is-invalid');
                                        field.closest('.col-md-6, .col-md-12').find('.invalid-feedback').text(errors[key][0]);
                                    }
                                });
                            } else {
                                showToast('An error occurred.', 'danger');
                            }
                        }
                    });
                }
                form.addClass('was-validated');
            });
        })();
    </script>
</div>
