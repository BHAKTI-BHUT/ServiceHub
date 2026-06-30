<!-- Quick Create Supervisor Modal -->
<div class="modal fade" id="quickCreateSupervisorModal" tabindex="-1" aria-labelledby="quickCreateSupervisorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickCreateSupervisorModalLabel">Create & Assign New Supervisor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickCreateSupervisorForm" novalidate>
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="quick_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="quick_name" name="name" required placeholder="Enter full name">
                            <div class="invalid-feedback">Please enter a name.</div>
                        </div>
                        <div class="col-md-12">
                            <label for="quick_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="quick_email" name="email" required placeholder="Enter email address">
                            <div class="invalid-feedback">Please enter a valid email.</div>
                        </div>
                        <div class="col-md-12">
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
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="save-quick-supervisor-btn">Save Supervisor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Generate random password helper
        $('#generate-password-btn').on('click', function() {
            var chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
            var pass = "";
            for (var i = 0; i < 10; i++) {
                pass += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            $('#quick_password').val(pass);
        });

        // Submit quick supervisor form
        $('#quickCreateSupervisorForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            form.find('.is-invalid').removeClass('is-invalid');

            if (this.checkValidity()) {
                $('#save-quick-supervisor-btn').prop('disabled', true).text('Saving...');
                $.ajax({
                    url: '{{ route('user.quick-create-supervisor') }}',
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        showToast(response.message);
                        
                        // Add option to Select2 and select it
                        var supervisor = response.user;
                        var text = supervisor.name + ' (' + (supervisor.mobile || 'No Mobile') + ')';
                        var newOption = new Option(text, supervisor.id, true, true);
                        $('#supervisors').append(newOption).trigger('change');

                        // Reset form & close modal
                        form[0].reset();
                        $('#quick_password').val('123456');
                        form.removeClass('was-validated');
                        $('#quickCreateSupervisorModal').modal('hide');
                        $('#save-quick-supervisor-btn').prop('disabled', false).text('Save Supervisor');
                    },
                    error: function(xhr) {
                        $('#save-quick-supervisor-btn').prop('disabled', false).text('Save Supervisor');
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(key) {
                                var field = form.find('[name="' + key + '"]');
                                if (field.length) {
                                    field.addClass('is-invalid');
                                    // if password input group or basic field
                                    var parent = field.closest('.col-md-12');
                                    parent.find('.invalid-feedback').text(errors[key][0]);
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
    });
</script>
