@extends('partials.layouts.master')
@section('title', 'Refund Management | ServiceHub')
@section('sub-title', 'Refunds')
@section('pagetitle', 'Refund Management')
@section('content')

<!-- Stats Widgets -->
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xxl-6 g-4 mb-4">
    <div class="col">
        <div class="card shadow-sm border-0 h-100 py-2" style="border-radius: 12px; border-left: 4px solid #1a1d2e !important;">
            <div class="card-body">
                <p class="text-muted text-uppercase fs-11 fw-bold mb-1">Total Requests</p>
                <h4 class="mb-0 fw-bold text-dark">{{ $stats['total'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-sm border-0 h-100 py-2" style="border-radius: 12px; border-left: 4px solid #ffc107 !important;">
            <div class="card-body">
                <p class="text-muted text-uppercase fs-11 fw-bold mb-1">Pending Requests</p>
                <h4 class="mb-0 fw-bold text-dark">{{ $stats['pending'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-sm border-0 h-100 py-2" style="border-radius: 12px; border-left: 4px solid #0dcaf0 !important;">
            <div class="card-body">
                <p class="text-muted text-uppercase fs-11 fw-bold mb-1">Approved Requests</p>
                <h4 class="mb-0 fw-bold text-dark">{{ $stats['approved'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-sm border-0 h-100 py-2" style="border-radius: 12px; border-left: 4px solid #198754 !important;">
            <div class="card-body">
                <p class="text-muted text-uppercase fs-11 fw-bold mb-1">Refunded Requests</p>
                <h4 class="mb-0 fw-bold text-dark">{{ $stats['refunded'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-sm border-0 h-100 py-2" style="border-radius: 12px; border-left: 4px solid #dc3545 !important;">
            <div class="card-body">
                <p class="text-muted text-uppercase fs-11 fw-bold mb-1">Rejected Requests</p>
                <h4 class="mb-0 fw-bold text-dark">{{ $stats['rejected'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card shadow-sm border-0 h-100 py-2" style="border-radius: 12px; border-left: 4px solid #20c997 !important;">
            <div class="card-body">
                <p class="text-muted text-uppercase fs-11 fw-bold mb-1">Total Refunded</p>
                <h4 class="mb-0 fw-bold text-dark">₹{{ number_format($stats['total_refunded'], 2) }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12">
        <div class="card mb-0 shadow-sm border-0 h-100" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-white py-3 border-bottom border-light">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="card-title mb-0 fw-bold text-dark"><i class="ri-refresh-line text-danger me-2 fs-18"></i>Refund Requests Management</h6>
                        <small class="text-muted">Manage user shifting cancellation and booking deposit refunds.</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="refund-table" class="table table-hover align-middle w-100 mb-0">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th class="py-3 px-4" style="font-weight: 700; width: 60px;">#</th>
                                <th class="py-3" style="font-weight: 700;">Booking No</th>
                                <th class="py-3" style="font-weight: 700;">Customer</th>
                                <th class="py-3" style="font-weight: 700;">Payment Type</th>
                                <th class="py-3" style="font-weight: 700;">Paid Amount</th>
                                <th class="py-3" style="font-weight: 700;">Req. Refund</th>
                                <th class="py-3" style="font-weight: 700;">Approved</th>
                                <th class="py-3" style="font-weight: 700;">Status</th>
                                <th class="py-3" style="font-weight: 700;">Date Requested</th>
                                <th class="py-3 px-4 text-end" style="font-weight: 700; width: 140px;">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
            <div class="modal-header bg-light border-bottom border-light py-3 px-4">
                <h5 class="modal-title fw-bold text-dark" id="detailModalLabel"><i class="ri-file-list-3-line text-danger me-2 fs-20 align-middle"></i>Refund Request Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="detailModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-danger" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-3 px-4">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
            <div class="modal-header bg-success text-white py-3 px-4">
                <h5 class="modal-title fw-bold"><i class="ri-checkbox-circle-line me-2"></i>Approve Refund Request</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="approveForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Approved Refund Amount (₹) <span class="text-danger">*</span></label>
                        <input type="number" name="approved_amount" id="approve_amount_input" class="form-control" step="0.01" min="0" required>
                        <small class="text-muted">Enter the amount to approve for refund. You can edit this if partial refund is applicable.</small>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">Admin Remarks</label>
                        <textarea name="admin_remarks" class="form-control" rows="3" placeholder="Add remarks for approval..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold">Approve Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
            <div class="modal-header bg-danger text-white py-3 px-4">
                <h5 class="modal-title fw-bold"><i class="ri-close-circle-line me-2"></i>Reject Refund Request</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-0">
                        <label class="form-label fw-bold">Rejection Reason / Remarks <span class="text-danger">*</span></label>
                        <textarea name="admin_remarks" class="form-control" rows="4" placeholder="Enter reason for rejection..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger px-4 fw-bold">Reject Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Process Refund Modal -->
<div class="modal fade" id="processModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
            <div class="modal-header bg-warning text-dark py-3 px-4">
                <h5 class="modal-title fw-bold"><i class="ri-hand-coin-line me-2"></i>Process Money Refund</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="processForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3 p-3 bg-light rounded" id="process_summary_wrap">
                        <div class="row">
                            <div class="col-6 fw-bold">Refund Method:</div>
                            <div class="col-6 text-end" id="lbl_refund_method">—</div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6 fw-bold">Approved Amount:</div>
                            <div class="col-6 text-end text-success fw-bold" id="lbl_refund_amount">—</div>
                        </div>
                    </div>
                    
                    <div id="manual_details_wrap" class="d-none">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Gateway / Payout Transaction ID <span class="text-danger">*</span></label>
                            <input type="text" name="gateway_refund_id" id="manual_tx_id" class="form-control" placeholder="Enter transaction ref. ID">
                            <small class="text-muted">Enter UPI/IMPS Ref ID / Razorpay Refund ID generated from your manual transfer.</small>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold">Admin Remarks</label>
                        <textarea name="admin_remarks" class="form-control" rows="3" placeholder="Add custom notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning px-4 fw-bold" id="process_submit_btn">Execute Refund</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    $(document).ready(function() {
        var table = initDataTable('#refund-table', '{{ route('admin.refunds') }}', [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'px-4 text-muted fw-semibold' },
            { data: 'booking_no', name: 'booking.booking_number', className: 'fw-bold text-primary' },
            { data: 'customer_name', name: 'customer.name', className: 'fw-semibold text-dark' },
            { data: 'payment_type', name: 'payment_type' },
            { data: 'total_paid_amount', name: 'total_paid_amount', className: 'fw-bold' },
            { data: 'requested_refund_amount', name: 'requested_refund_amount', className: 'fw-bold' },
            { data: 'approved_refund_amount', name: 'approved_refund_amount', className: 'fw-bold' },
            { data: 'status', name: 'status' },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'px-4 text-end' }
        ]);

        var activeRefundId = null;

        // View Details Modal Trigger
        $(document).on('click', '.view-refund-btn', function() {
            var id = $(this).data('id');
            $('#detailModalBody').html('<div class="text-center py-4"><div class="spinner-border text-danger" role="status"><span class="visually-hidden">Loading...</span></div></div>');
            $('#detailModal').modal('show');

            $.get('{{ url("admin/refunds") }}/' + id, function(r) {
                if (r.success) {
                    var data = r.data;
                    var methodMap = { original_source: '⬅ Original Source (Razorpay)', upi: '📱 UPI ID', bank_transfer: '🏦 Bank Transfer' };
                    
                    // Status Badge Mapping
                    var statusBadges = {
                        pending: '<span class="badge bg-warning-focus text-warning py-1.5 px-3 fs-12"><i class="ri-time-line me-1"></i>Pending</span>',
                        approved: '<span class="badge bg-info-focus text-info py-1.5 px-3 fs-12"><i class="ri-checkbox-circle-line me-1"></i>Approved</span>',
                        processing: '<span class="badge bg-secondary-focus text-secondary py-1.5 px-3 fs-12"><i class="ri-refresh-line me-1"></i>Processing</span>',
                        refunded: '<span class="badge bg-success-focus text-success py-1.5 px-3 fs-12"><i class="ri-checkbox-circle-fill me-1"></i>Refunded</span>',
                        rejected: '<span class="badge bg-danger-focus text-danger py-1.5 px-3 fs-12"><i class="ri-close-circle-line me-1"></i>Rejected</span>'
                    };

                    var html = `
                        <!-- Top Header Status Panel -->
                        <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded-3 border border-light-subtle flex-wrap gap-2">
                            <div>
                                <span class="text-muted fs-12">Refund Request ID</span>
                                <h5 class="fw-bold mb-0 text-dark">#${data.id}</h5>
                            </div>
                            <div class="text-end">
                                <span class="text-muted fs-12 d-block mb-1">Status</span>
                                ${statusBadges[data.status] || data.status}
                            </div>
                        </div>

                        <!-- 3-Column Info Cards -->
                        <div class="row g-3 mb-4">
                            <!-- Column 1: Customer Details -->
                            <div class="col-md-4">
                                <div class="card h-100 border border-light-subtle shadow-none bg-light-focus" style="border-radius: 10px;">
                                    <div class="card-body p-3">
                                        <h6 class="fw-bold text-dark mb-3"><i class="ri-user-line text-primary me-2 fs-16"></i>Customer Info</h6>
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Name</small>
                                            <span class="fw-semibold text-dark fs-13">${r.customer_name}</span>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Mobile</small>
                                            <span class="fw-semibold text-dark fs-13">${r.customer_mobile}</span>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>

                            <!-- Column 2: Booking Info -->
                            <div class="col-md-4">
                                <div class="card h-100 border border-light-subtle shadow-none bg-light-focus" style="border-radius: 10px;">
                                    <div class="card-body p-3">
                                        <h6 class="fw-bold text-dark mb-3"><i class="ri-ticket-2-line text-success me-2 fs-16"></i>Booking & Fees</h6>
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Booking No.</small>
                                            <span class="fw-semibold text-primary fs-13">#${r.booking_no}</span>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <small class="text-muted d-block">Paid</small>
                                                <span class="fw-bold text-dark fs-13">₹${parseFloat(data.total_paid_amount || 0).toFixed(2)}</span>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted d-block">Requested</small>
                                                <span class="fw-bold text-danger fs-13">₹${parseFloat(data.requested_refund_amount || 0).toFixed(2)}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Column 3: Payout Channel -->
                            <div class="col-md-4">
                                <div class="card h-100 border border-light-subtle shadow-none bg-light-focus" style="border-radius: 10px;">
                                    <div class="card-body p-3">
                                        <h6 class="fw-bold text-dark mb-3"><i class="ri-wallet-3-line text-purple me-2 fs-16"></i>Payout Target</h6>
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Refund Channel</small>
                                            <span class="badge bg-light text-dark fs-11">${methodMap[data.refund_method] || data.refund_method}</span>
                                        </div>
                                        ${data.upi_id ? `
                                            <div>
                                                <small class="text-muted d-block">UPI ID</small>
                                                <code class="text-purple fs-12">${data.upi_id}</code>
                                            </div>
                                        ` : ''}
                                        ${data.bank_account_no ? `
                                            <div>
                                                <small class="text-muted d-block">Account / IFSC</small>
                                                <code class="text-teal fs-12">${data.bank_account_no}</code><br>
                                                <code class="text-muted fs-11">${data.bank_ifsc || ''}</code>
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Gateway and Approval details if any -->
                        ${(data.approved_refund_amount > 0 || data.gateway_payment_id || data.gateway_refund_id) ? `
                            <div class="card border border-light-subtle shadow-none mb-4" style="border-radius: 10px;">
                                <div class="card-body p-3 bg-light-focus">
                                    <h6 class="fw-bold text-dark mb-3"><i class="ri-shield-check-line text-info me-2 fs-16"></i>Processing / Settlement Info</h6>
                                    <div class="row g-3">
                                        ${data.approved_refund_amount > 0 ? `
                                            <div class="col-sm-4">
                                                <small class="text-muted d-block">Approved Amount</small>
                                                <span class="fw-bold text-success fs-14">₹${parseFloat(data.approved_refund_amount).toFixed(2)}</span>
                                            </div>
                                        ` : ''}
                                        ${data.gateway_payment_id ? `
                                            <div class="col-sm-4">
                                                <small class="text-muted d-block">Payment ID</small>
                                                <code class="text-dark fs-12">${data.gateway_payment_id}</code>
                                            </div>
                                        ` : ''}
                                        ${data.gateway_refund_id ? `
                                            <div class="col-sm-4">
                                                <small class="text-muted d-block">Refund Ref ID</small>
                                                <code class="text-dark fs-12">${data.gateway_refund_id}</code>
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        ` : ''}

                        <!-- Text Details (Reasons, remarks) -->
                        <div class="d-flex flex-column gap-3">
                            <div class="p-3 bg-danger-focus rounded-3 border-start border-4 border-danger">
                                <h6 class="fw-bold text-danger mb-1 fs-13"><i class="ri-error-warning-line me-1 align-middle"></i>Cancellation Reason</h6>
                                <p class="mb-1 text-dark fw-semibold fs-13">${data.cancellation_reason}</p>
                                ${data.reason_details ? `<p class="mb-0 text-muted fs-12 mt-1">${data.reason_details}</p>` : ''}
                            </div>

                            ${data.admin_remarks ? `
                                <div class="p-3 bg-success-focus rounded-3 border-start border-4 border-success">
                                    <h6 class="fw-bold text-success mb-1 fs-13"><i class="ri-chat-check-line me-1 align-middle"></i>Admin Settlement Notes</h6>
                                    <p class="mb-0 text-muted fs-12">${data.admin_remarks}</p>
                                </div>
                            ` : ''}
                        </div>

                        <div class="mt-4 pt-2 text-end text-muted fs-11 border-top border-light">
                            <span>Created: ${r.formatted_created_at}</span>
                            <span class="mx-2">|</span>
                            <span>Updated: ${r.formatted_updated_at}</span>
                        </div>
                    `;
                    $('#detailModalBody').html(html);
                } else {
                    $('#detailModalBody').html('<div class="alert alert-danger">Failed to fetch refund details.</div>');
                }
            });
        });

        // Approve Request Modal
        $(document).on('click', '.approve-refund-btn', function() {
            activeRefundId = $(this).data('id');
            var reqAmount = $(this).data('amount');
            $('#approve_amount_input').val(reqAmount);
            $('#approveModal').modal('show');
        });

        $('#approveForm').submit(function(e) {
            e.preventDefault();
            var btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).text('Approving...');

            $.ajax({
                type: 'POST',
                url: '{{ url("admin/refunds") }}/' + activeRefundId + '/approve',
                data: $(this).serialize(),
                success: function(resp) {
                    $('#approveModal').modal('hide');
                    if (resp.success) {
                        showToast(resp.message, 'success');
                        table.ajax.reload();
                    } else {
                        showToast(resp.message, 'danger');
                    }
                },
                error: function(xhr) {
                    showToast(xhr.responseJSON?.message || 'Server error', 'danger');
                },
                complete: function() {
                    btn.prop('disabled', false).text('Approve Request');
                }
            });
        });

        // Reject Request Modal
        $(document).on('click', '.reject-refund-btn', function() {
            activeRefundId = $(this).data('id');
            $('#rejectForm').find('textarea').val('');
            $('#rejectModal').modal('show');
        });

        $('#rejectForm').submit(function(e) {
            e.preventDefault();
            var btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).text('Rejecting...');

            $.ajax({
                type: 'POST',
                url: '{{ url("admin/refunds") }}/' + activeRefundId + '/reject',
                data: $(this).serialize(),
                success: function(resp) {
                    $('#rejectModal').modal('hide');
                    if (resp.success) {
                        showToast(resp.message, 'success');
                        table.ajax.reload();
                    } else {
                        showToast(resp.message, 'danger');
                    }
                },
                error: function(xhr) {
                    showToast(xhr.responseJSON?.message || 'Server error', 'danger');
                },
                complete: function() {
                    btn.prop('disabled', false).text('Reject Request');
                }
            });
        });

        // Process Refund Modal
        $(document).on('click', '.process-refund-btn', function() {
            activeRefundId = $(this).data('id');
            var approvedAmt = $(this).data('amount');
            var refundMethod = $(this).data('method');

            $('#lbl_refund_amount').text('₹' + parseFloat(approvedAmt).toFixed(2));
            
            if (refundMethod === 'original_source') {
                $('#lbl_refund_method').text('Original Source (Razorpay API)');
                $('#manual_details_wrap').addClass('d-none');
                $('#manual_tx_id').prop('required', false);
                $('#process_submit_btn').removeClass('btn-warning').addClass('btn-success').text('Refund via Razorpay API');
            } else {
                var methodLabels = { upi: 'UPI Payout', bank_transfer: 'Bank Account Transfer' };
                $('#lbl_refund_method').text(methodLabels[refundMethod] || refundMethod);
                $('#manual_details_wrap').removeClass('d-none');
                $('#manual_tx_id').prop('required', true).val('');
                $('#process_submit_btn').removeClass('btn-success').addClass('btn-warning').text('Confirm Manual Payout');
            }

            $('#processModal').modal('show');
        });

        $('#processForm').submit(function(e) {
            e.preventDefault();
            var btn = $('#process_submit_btn');
            var origText = btn.text();
            btn.prop('disabled', true).text('Processing Refund...');

            $.ajax({
                type: 'POST',
                url: '{{ url("admin/refunds") }}/' + activeRefundId + '/process',
                data: $(this).serialize(),
                success: function(resp) {
                    $('#processModal').modal('hide');
                    if (resp.success) {
                        showToast(resp.message, 'success');
                        setTimeout(() => location.reload(), 1200);
                    } else {
                        showToast(resp.message, 'danger');
                    }
                },
                error: function(xhr) {
                    showToast(xhr.responseJSON?.message || 'Gateway/Server error', 'danger');
                },
                complete: function() {
                    btn.prop('disabled', false).text(origText);
                }
            });
        });
    });
</script>
<style>
    #refund-table { border-collapse: collapse !important; }
    #refund-table th { font-size: 0.8rem; letter-spacing: 0.5px; text-transform: uppercase; border-bottom: 2px solid #f1f2f5; }
    #refund-table td { font-size: 0.88rem; padding: 14px 10px; border-bottom: 1px solid #f1f2f5; }
    #refund-table tbody tr:hover { background-color: #fafbfd; }
    .badge { font-weight: 600; padding: 5px 10px; border-radius: 4px; font-size: 0.75rem; letter-spacing: 0.3px; }
    .bg-warning-focus { background-color: #fff3cd !important; color: #664d03 !important; border: 1px solid #ffecb5; }
    .bg-info-focus { background-color: #cff4fc !important; color: #055160 !important; border: 1px solid #b6effb; }
    .bg-success-focus { background-color: #d1e7dd !important; color: #0f5132 !important; border: 1px solid #badbcc; }
    .bg-danger-focus { background-color: #f8d7da !important; color: #842029 !important; border: 1px solid #f5c2c7; }
    .bg-purple-focus { background-color: #e2d9f3 !important; color: #4e2994 !important; border: 1px solid #d3c4ec; }
    .bg-teal-focus { background-color: #d1f2e5 !important; color: #086b4a !important; border: 1px solid #b6e2ce; }
    .bg-orange-focus { background-color: #ffe6d9 !important; color: #bf360c !important; border: 1px solid #f8c2b5; }
</style>
@endsection
