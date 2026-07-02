<div id="drawer-form-content">
    <div class="row g-3 fs-13">
        <!-- Customer Info Section -->
        <div class="col-12">
            <h6 class="text-uppercase fs-11 text-muted mb-2">Customer Profile</h6>
            <div class="bg-light p-3 rounded d-flex align-items-center gap-3">
                <img src="{{ $bookingRequest->customer && $bookingRequest->customer->image ? asset($bookingRequest->customer->image) : asset('assets/images/avatar/dummy-avatar.jpg') }}" 
                    class="rounded-circle" width="50" height="50" style="object-fit: cover;">
                <div>
                    <h6 class="mb-1">{{ $bookingRequest->customer ? $bookingRequest->customer->name : 'N/A' }}</h6>
                    <span class="text-muted"><i class="ri-phone-line me-1"></i>{{ $bookingRequest->customer ? ($bookingRequest->customer->mobile ?? 'N/A') : 'N/A' }}</span>
                </div>
            </div>
        </div>

        {{-- Booking Contact Number (if different from login number) --}}
        @if ($bookingRequest->phone_number)
        <div class="col-12">
            <div class="d-flex align-items-center gap-2 bg-light rounded px-3 py-2">
                <i class="ri-smartphone-line text-primary fs-16"></i>
                <div>
                    <span class="text-muted d-block fs-11">Booking Contact Number</span>
                    <span class="fw-semibold">{{ $bookingRequest->phone_number }}</span>
                </div>
            </div>
        </div>
        @endif

        <!-- Shifting Details Section -->
        <div class="col-12 mt-3">
            <h6 class="text-uppercase fs-11 text-muted mb-2">Shifting Specifications</h6>
            <div class="card border shadow-none mb-0">
                <div class="card-body p-3">
                    <div class="d-flex flex-column gap-3">
                        <div>
                            <span class="text-muted d-block fs-11">Pickup Location</span>
                            <span class="fw-medium text-success"><i class="ri-map-pin-user-line me-1"></i>{{ $bookingRequest->pickup_location }}</span>
                            @if ($bookingRequest->pickup_latitude && $bookingRequest->pickup_longitude)
                                <a href="https://www.google.com/maps/search/?api=1&query={{ $bookingRequest->pickup_latitude }},{{ $bookingRequest->pickup_longitude }}" target="_blank" class="d-block small text-primary mt-1">
                                    <i class="ri-external-link-line me-1"></i>View on Google Maps
                                </a>
                            @endif
                        </div>
                        <div>
                            <span class="text-muted d-block fs-11">Drop Location</span>
                            <span class="fw-medium text-danger"><i class="ri-map-pin-5-line me-1"></i>{{ $bookingRequest->drop_location }}</span>
                            @if ($bookingRequest->drop_latitude && $bookingRequest->drop_longitude)
                                <a href="https://www.google.com/maps/search/?api=1&query={{ $bookingRequest->drop_latitude }},{{ $bookingRequest->drop_longitude }}" target="_blank" class="d-block small text-primary mt-1">
                                    <i class="ri-external-link-line me-1"></i>View on Google Maps
                                </a>
                            @endif
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <span class="text-muted d-block fs-11">Shifting Date</span>
                                <span class="fw-semibold"><i class="ri-calendar-line me-1 text-primary"></i>{{ date('d M Y', strtotime($bookingRequest->shifting_date)) }}</span>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block fs-11">Shifting Time</span>
                                <span class="fw-semibold">
                                    <i class="ri-time-line me-1 text-primary"></i>
                                    {{ $bookingRequest->shifting_time ? date('h:i A', strtotime($bookingRequest->shifting_time)) : '—' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Est Amount & Status -->
        <div class="col-12 mt-3">
            <div class="card border border-primary shadow-none bg-primary bg-opacity-10 mb-0">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-primary d-block fs-11">Estimated Amount</span>
                        <h4 class="mb-0 text-primary fw-bold">₹{{ number_format($bookingRequest->estimated_amount, 2) }}</h4>
                    </div>
                    <div>
                        @if ($bookingRequest->status === 'pending')
                            <span class="badge bg-warning text-dark px-3 py-2 fs-12">Pending Approval</span>
                        @elseif ($bookingRequest->status === 'approved')
                            <span class="badge bg-success px-3 py-2 fs-12">Approved</span>
                        @else
                            <span class="badge bg-danger px-3 py-2 fs-12">Rejected</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Approve / Reject Actions -->
        @if ($bookingRequest->status === 'pending')
            <div class="col-12 mt-4 text-center">
                <hr class="mb-4 mt-0">
                <div class="d-flex gap-3 justify-content-center">
                    <button type="button" id="btnRejectRequest" class="btn btn-danger px-4">
                        <i class="ri-close-circle-line me-1"></i>Reject Request
                    </button>
                    <button type="button" id="btnApproveRequest" class="btn btn-success px-4">
                        <i class="ri-checkbox-circle-line me-1"></i>Approve Request
                    </button>
                </div>
            </div>
        @endif
    </div>

    <script>
        $(document).ready(function() {
            // Get offcanvas/drawer instance to close it later
            const offcanvasEl = document.getElementById('commonDrawer');
            const drawer = bootstrap.Offcanvas.getInstance(offcanvasEl);

            $('#btnApproveRequest').on('click', function() {
                var $btn = $(this);
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> Approving...');
                
                $.ajax({
                    url: '{{ route('booking-request.approve', $bookingRequest->id) }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        showToast(response.message || 'Request approved successfully!');
                        drawer.hide();
                        // Reload Table
                        if ($('#request-table').length) {
                            $('#request-table').DataTable().ajax.reload(null, false);
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html('<i class="ri-checkbox-circle-line me-1"></i>Approve Request');
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Approval failed.';
                        showToast(msg, 'danger');
                    }
                });
            });

            $('#btnRejectRequest').on('click', function() {
                var $btn = $(this);
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> Rejecting...');
                
                $.ajax({
                    url: '{{ route('booking-request.reject', $bookingRequest->id) }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        showToast(response.message || 'Request rejected successfully!');
                        drawer.hide();
                        // Reload Table
                        if ($('#request-table').length) {
                            $('#request-table').DataTable().ajax.reload(null, false);
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html('<i class="ri-close-circle-line me-1"></i>Reject Request');
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Rejection failed.';
                        showToast(msg, 'danger');
                    }
                });
            });
        });
    </script>
</div>
