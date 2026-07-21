@extends('partials.layouts.master')

@section('title', 'Booking ' . $booking->booking_number . ' | Supervisor Panel')
@section('sub-title', 'Booking Details')
@section('pagetitle', 'Bookings')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('supervisor.booking.index') }}">My Bookings</a></li>
                <li class="breadcrumb-item active">{{ $booking->booking_number }}</li>
            </ol>
        </nav>
    </div>

    {{-- ── Left Column: Details ── --}}
    <div class="col-lg-8">

        {{-- Booking Info Card --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Shifting Details</h5>
                <span class="font-monospace fw-semibold text-primary">{{ $booking->booking_number }}</span>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <span class="text-muted d-block fs-11">Pickup Location</span>
                        <span class="fw-semibold text-success fs-14"><i class="ri-map-pin-user-line me-1"></i>{{ $booking->pickup_location }}</span>
                        @if($booking->pickup_contact_name || $booking->pickup_contact_mobile)
                            <div class="mt-2 text-dark fs-12"><i class="ri-user-line me-1 text-muted"></i><strong>Contact:</strong> {{ $booking->pickup_contact_name ?? '—' }} ({{ $booking->pickup_contact_mobile ?? '—' }})</div>
                        @endif
                        @if($booking->pickup_latitude && $booking->pickup_longitude)
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $booking->pickup_latitude }},{{ $booking->pickup_longitude }}" target="_blank" class="d-block small text-primary mt-1"><i class="ri-external-link-line me-1"></i>View on Maps</a>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted d-block fs-11">Drop Location</span>
                        <span class="fw-semibold text-danger fs-14"><i class="ri-map-pin-5-line me-1"></i>{{ $booking->drop_location }}</span>
                        @if($booking->drop_contact_name || $booking->drop_contact_mobile)
                            <div class="mt-2 text-dark fs-12"><i class="ri-user-line me-1 text-muted"></i><strong>Contact:</strong> {{ $booking->drop_contact_name ?? '—' }} ({{ $booking->drop_contact_mobile ?? '—' }})</div>
                        @endif
                        @if($booking->drop_latitude && $booking->drop_longitude)
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $booking->drop_latitude }},{{ $booking->drop_longitude }}" target="_blank" class="d-block small text-primary mt-1"><i class="ri-external-link-line me-1"></i>View on Maps</a>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted d-block fs-11">Shifting Date</span>
                        <span class="fw-medium text-dark"><i class="ri-calendar-line me-1 text-primary"></i>{{ date('d M Y', strtotime($booking->shifting_date)) }}</span>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted d-block fs-11">Shifting Time</span>
                        <span class="fw-medium text-dark"><i class="ri-time-line me-1 text-primary"></i>{{ date('h:i A', strtotime($booking->shifting_time)) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Items & Addons --}}
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0"><i class="ri-box-3-fill text-warning me-2"></i>Shifting Items & Add-Ons</h5>
                <div>
                    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill" id="volume-badge">Volume: {{ $booking->total_volume_score }} pts</span>
                    @if($booking->category)
                        <span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill ms-1">{{ $booking->category->category_name }}</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4 bg-light p-3 rounded-3 mx-0">
                    <div class="col-md-4 text-center border-end">
                        <span class="text-muted fs-11 d-block mb-1">Vehicle</span>
                        <span class="fw-semibold text-dark"><i class="ri-truck-line me-1 text-primary"></i>{{ $booking->vehicle ? $booking->vehicle->vehicle_name : 'None' }}</span>
                    </div>
                    <div class="col-md-4 text-center border-end">
                        <span class="text-muted fs-11 d-block mb-1">Distance</span>
                        <span class="fw-semibold text-dark"><i class="ri-map-pin-line me-1 text-success"></i>{{ $booking->total_distance ?? 0 }} km</span>
                    </div>
                    <div class="col-md-4 text-center">
                        <span class="text-muted fs-11 d-block mb-1">Floors</span>
                        <span class="fw-semibold text-dark"><i class="ri-building-line me-1 text-warning"></i>{{ $booking->floors ?? 0 }} floors</span>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-7">
                        <h6 class="fw-bold mb-3"><i class="ri-box-3-fill text-warning me-2"></i>Items <span class="badge bg-warning-subtle text-warning fs-11 rounded-pill">{{ $booking->items->count() }}</span></h6>
                        @if($booking->items->isEmpty())
                            <div class="text-muted fs-12 py-3 bg-light text-center rounded">No items selected.</div>
                        @else
                            <div class="table-responsive border rounded" style="max-height:300px;overflow-y:auto;">
                                <table class="table table-sm table-hover align-middle mb-0">
                                    <thead class="bg-light sticky-top"><tr>
                                        <th class="ps-3 fs-11 text-uppercase text-muted py-2 border-0">Item</th>
                                        <th class="text-center fs-11 text-uppercase text-muted py-2 border-0">Qty</th>
                                        <th class="pe-3 text-end fs-11 text-uppercase text-muted py-2 border-0">Score</th>
                                    </tr></thead>
                                    <tbody>
                                        @foreach($booking->items as $itm)
                                        <tr>
                                            <td class="ps-3 py-2 fw-medium text-dark fs-12">{{ $itm->item_name }}</td>
                                            <td class="py-2 text-center fs-12"><span class="badge bg-light text-dark border">{{ $itm->pivot->quantity }}</span></td>
                                            <td class="pe-3 py-2 text-end text-muted fs-12">{{ $itm->pivot->calculated_volume_score }} pts</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-5">
                        <h6 class="fw-bold mb-3"><i class="ri-service-fill text-info me-2"></i>Add-Ons <span class="badge bg-info-subtle text-info fs-11 rounded-pill">{{ $booking->addOns->count() }}</span></h6>
                        @if($booking->addOns->isEmpty())
                            <div class="text-muted fs-12 py-3 bg-light text-center rounded">No add-ons.</div>
                        @else
                            <div class="list-group list-group-flush border rounded overflow-hidden">
                                @foreach($booking->addOns as $ad)
                                <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3 fs-12">
                                    <span class="fw-medium text-dark"><i class="ri-checkbox-circle-fill text-success me-2"></i>{{ $ad->addon_name }}</span>
                                    <span class="badge bg-success-subtle text-success">₹{{ number_format($ad->pivot->price ?? $ad->price, 0) }}</span>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Update Items Button (only when in_progress) --}}
                @if($booking->status === 'in_progress')
                <div class="mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#updateItemsModal">
                        <i class="ri-pencil-line me-1"></i> Update Items / Add-Ons During Shifting
                    </button>
                </div>
                @endif
            </div>
        </div>

        {{-- Box Photo Proofs --}}
        @if(!empty($booking->box_photos))
        <div class="card mb-4">
            <div class="card-header"><h5 class="card-title mb-0"><i class="ri-gallery-line text-primary me-2"></i>Uploaded Box Photo Proofs</h5></div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach($booking->box_photos as $photo)
                    <div class="col-6 col-md-3">
                        <a href="{{ asset($photo) }}" target="_blank">
                            <img src="{{ asset($photo) }}" class="img-fluid rounded border" style="height:100px; width:100%; object-fit:cover;">
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Tracking Timeline --}}
        <div class="card mb-0">
            <div class="card-header"><h5 class="card-title mb-0">Tracking Progress</h5></div>
            <div class="card-body py-4">
                @if($booking->tracking_status === 'cancelled')
                    <div class="alert alert-danger d-flex align-items-center mb-0"><i class="ri-error-warning-line fs-20 me-2"></i><div><strong>Cancelled.</strong></div></div>
                @else
                    @php
                        $statusOrder = [
                            'pending_confirmation' => 'Confirmation Pending',
                            'confirmed'            => 'Confirmed',
                            'trip_started'         => 'Trip Started',
                            'shifting_started'     => 'Shifting Started',
                            'pickup_completed'     => 'Pickup Done',
                            'shifting_completed'   => 'Completed'
                        ];
                        $keys = array_keys($statusOrder);
                        $currentIdx = array_search($booking->tracking_status, $keys);
                        if ($currentIdx === false) {
                            if ($booking->tracking_status === 'completed') $currentIdx = 5;
                            else $currentIdx = 0;
                        }
                    @endphp
                    <div class="d-flex justify-content-between align-items-center position-relative mb-4" style="margin-top:15px;">
                        <div class="progress position-absolute start-0 end-0" style="height:4px;z-index:1;top:50%;transform:translateY(-50%);">
                            <div class="progress-bar bg-success" style="width:{{ ($currentIdx / (count($keys)-1)) * 100 }}%"></div>
                        </div>
                        @foreach($keys as $idx => $stepKey)
                        <div class="text-center position-relative" style="z-index:2;width:{{ 100/count($keys) }}%">
                            <div class="rounded-circle border border-2 mx-auto mb-2 {{ $idx <= $currentIdx ? 'bg-success text-white border-success' : 'bg-white text-muted border-light' }}" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;">
                                @if($idx < $currentIdx)<i class="ri-check-line"></i>@else<span>{{ $idx+1 }}</span>@endif
                            </div>
                            <span class="fs-12 fw-medium {{ $idx == $currentIdx ? 'text-success fw-semibold' : 'text-muted' }}">{{ $statusOrder[$stepKey] }}</span>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Right Column: Actions & Info ── --}}
    <div class="col-lg-4">

        {{-- Customer Card --}}
        <div class="card mb-4">
            <div class="card-header"><h5 class="card-title mb-0">Customer Details</h5></div>
            <div class="card-body">
                @if($booking->customer)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{ $booking->customer->image ? asset($booking->customer->image) : asset('assets/images/avatar/dummy-avatar.jpg') }}" class="rounded-circle border" width="50" height="50" style="object-fit:cover;">
                        <div>
                            <h6 class="mb-1">{{ $booking->customer->name }}</h6>
                            <span class="text-muted small"><i class="ri-phone-line me-1"></i>{{ $booking->customer->mobile ?? '—' }}</span>
                        </div>
                    </div>
                    <div class="fs-12 text-muted d-flex flex-column gap-2">
                        <div><strong class="text-dark">Email:</strong> {{ $booking->customer->email }}</div>
                        <div><strong class="text-dark">City:</strong> {{ $booking->customer->city ?? 'N/A' }}</div>
                    </div>
                @else
                    <div class="text-muted">No customer linked.</div>
                @endif
            </div>
        </div>

        {{-- Amount Card --}}
        <div class="card mb-4 border border-primary bg-primary bg-opacity-10 shadow-none">
            <div class="card-body p-3">
                <span class="text-primary d-block fs-11">Total Amount</span>
                <h2 class="mb-0 text-primary fw-bold" id="total-amount-display">₹{{ number_format($booking->amount, 2) }}</h2>
                @if($booking->remaining_amount > 0)
                <span class="text-danger fs-12 d-block mt-1"><i class="ri-error-warning-line me-1"></i>Remaining: ₹{{ number_format($booking->remaining_amount, 2) }}</span>
                @endif
            </div>
        </div>

        {{-- Lifecycle Actions Card --}}
        <div class="card mb-0">
            <div class="card-header"><h5 class="card-title mb-0">Shifting Actions</h5></div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($booking->supervisor_acceptance_status === 'pending' || $booking->supervisor_acceptance_status === null)
                        <button class="btn btn-success btn-accept-action" data-id="{{ $booking->id }}"><i class="ri-check-double-line me-1"></i>Accept Assignment</button>
                        <button class="btn btn-outline-danger btn-reject-action" data-id="{{ $booking->id }}"><i class="ri-close-circle-line me-1"></i>Reject Assignment</button>

                    @elseif($booking->supervisor_acceptance_status === 'accepted')
                        @if($booking->tracking_status === 'confirmed' || $booking->tracking_status === 'pending_confirmation')
                            <button class="btn btn-info text-white btn-action" id="btn-start-trip" data-id="{{ $booking->id }}" data-url="{{ route('supervisor.booking.startTrip', $booking->id) }}">
                                <i class="ri-roadster-line me-1"></i>Start Trip (Vehicle Departed)
                            </button>
                        @elseif($booking->tracking_status === 'trip_started')
                            <!-- OTP verification to start shifting -->
                            <div class="p-3 border rounded bg-white text-start">
                                <h6 class="fw-semibold mb-2 text-dark"><i class="ri-lock-line text-warning me-1"></i> Verify Customer OTP</h6>
                                <p class="text-muted fs-11 mb-3">Ask customer for the OTP to start shifting/pickup.</p>
                                <div class="mb-3">
                                    <input type="text" id="otp-input" class="form-control form-control-sm text-center font-monospace fw-bold fs-16" placeholder="Enter 4-Digit OTP" maxlength="4" style="letter-spacing: 4px;">
                                </div>
                                <button class="btn btn-warning btn-sm w-100" id="btn-verify-otp" data-id="{{ $booking->id }}" data-url="{{ route('supervisor.booking.verifyOtp', $booking->id) }}">
                                    <i class="ri-shield-keyhole-line me-1"></i>Verify & Start Shifting
                                </button>
                            </div>
                        @elseif($booking->tracking_status === 'shifting_started')
                            <!-- Proof uploads (photos) to mark pickup completed -->
                            <div class="p-3 border rounded bg-white text-start">
                                <h6 class="fw-semibold mb-2 text-dark"><i class="ri-camera-line text-primary me-1"></i> Upload Box Photo Proofs</h6>
                                <p class="text-muted fs-11 mb-3">Select photos of packed/loaded boxes to mark pickup as completed.</p>
                                <form id="proof-upload-form" enctype="multipart/form-data">
                                    @csrf
                                    <input type="file" name="box_photos[]" class="form-control form-control-sm mb-3" multiple accept="image/*" required>
                                    <button type="submit" class="btn btn-primary btn-sm w-100" id="btn-upload-proof" data-url="{{ route('supervisor.booking.uploadProof', $booking->id) }}">
                                        <i class="ri-upload-cloud-line me-1"></i>Upload & Complete Pickup
                                    </button>
                                </form>
                            </div>
                        @elseif($booking->tracking_status === 'pickup_completed')
                            <!-- Travel to drop location done, unloading, setup, and payment step -->
                            <div class="p-3 border rounded bg-white text-start">
                                <h6 class="fw-semibold mb-2 text-dark"><i class="ri-map-pin-5-line text-danger me-1"></i> Unload & Setup Items</h6>
                                <p class="text-muted fs-11 mb-3">Deliver to drop location and arrange all customer products. Then confirm payment.</p>
                                
                                @if($booking->remaining_payment_status === 'paid')
                                    <div class="alert alert-success py-2 fs-12 mb-3">
                                        <i class="ri-checkbox-circle-line me-1"></i><strong>Payment Verified!</strong> Customer has paid online / direct to Admin.
                                    </div>
                                    <button class="btn btn-success btn-sm w-100 btn-action" id="btn-complete-admin" data-id="{{ $booking->id }}" data-url="{{ route('supervisor.booking.completeShifting', $booking->id) }}">
                                        <i class="ri-check-double-line me-1"></i>Mark Shifting Completed
                                    </button>
                                @else
                                    <div class="alert alert-warning py-2 fs-12 mb-3">
                                        <i class="ri-information-line me-1"></i><strong>Remaining Payment Pending:</strong> ₹{{ number_format($booking->remaining_amount, 2) }}
                                    </div>
                                    <button class="btn btn-success btn-sm w-100 btn-action" id="btn-collect-cash" data-id="{{ $booking->id }}" data-url="{{ route('supervisor.booking.collectCash', $booking->id) }}">
                                        <i class="ri-money-rupee-circle-line me-1"></i>Collect Cash & Complete Shifting
                                    </button>
                                    <p class="text-center text-muted fs-10 mt-2 mb-0">Note: Vendor wallet will be debited 20% platform fee.</p>
                                @endif
                            </div>
                        @elseif($booking->status === 'completed' || $booking->tracking_status === 'shifting_completed')
                            <div class="alert alert-success text-center mb-0 py-2 fs-12">
                                <i class="ri-medal-line me-1"></i><strong>Shifting is Completed!</strong>
                                @if($booking->payment_method)
                                    <div class="mt-1 small text-muted">Payment Mode: {{ ucfirst($booking->payment_method) }}</div>
                                @endif
                            </div>
                        @else
                            <div class="alert alert-light text-center mb-0 py-2 fs-12 text-muted">No actions available yet. Waiting for booking confirmation.</div>
                        @endif

                    @elseif($booking->supervisor_acceptance_status === 'rejected')
                        <div class="alert alert-danger text-center mb-0 py-2 fs-12"><i class="ri-close-circle-line me-1"></i>You have rejected this assignment.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Update Items Modal ── --}}
@if($booking->status === 'in_progress')
<div class="modal fade" id="updateItemsModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ri-pencil-line me-2 text-warning"></i>Update Items During Shifting</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning py-2 fs-12"><i class="ri-information-line me-1"></i>Change item quantities or add/remove services. The total price will be recalculated automatically.</div>

                <div class="row g-4">
                    <div class="col-md-7">
                        <h6 class="fw-semibold mb-3">Items</h6>
                        @foreach($itemSizes as $size)
                            @if($size->items->count())
                            <div class="mb-3">
                                <div class="fw-medium text-muted fs-12 mb-2 pb-1 border-bottom">{{ $size->size_name }}</div>
                                @foreach($size->items as $item)
                                @php $currentQty = $booking->items->firstWhere('id', $item->id)?->pivot->quantity ?? 0; @endphp
                                <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                    <label class="mb-0 fw-medium fs-13 text-dark">{{ $item->item_name }}</label>
                                    <div class="input-group" style="max-width:130px;">
                                        <button type="button" class="btn btn-sm btn-outline-secondary qty-minus" data-item="{{ $item->id }}">-</button>
                                        <input type="number" name="item_qty[{{ $item->id }}]" id="qty-{{ $item->id }}" value="{{ $currentQty }}" min="0" max="50" class="form-control form-control-sm text-center item-qty-input" data-item="{{ $item->id }}" style="width:55px;">
                                        <button type="button" class="btn btn-sm btn-outline-secondary qty-plus" data-item="{{ $item->id }}">+</button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="col-md-5">
                        <h6 class="fw-semibold mb-3">Add-on Services</h6>
                        @foreach($allAddons as $addon)
                        @php $checked = $booking->addOns->contains('id', $addon->id); @endphp
                        <div class="form-check mb-2">
                            <input class="form-check-input addon-check" type="checkbox" value="{{ $addon->id }}" id="addon-{{ $addon->id }}" {{ $checked ? 'checked' : '' }}>
                            <label class="form-check-label fs-13" for="addon-{{ $addon->id }}">
                                {{ $addon->addon_name }}
                                <span class="text-muted fs-11">• ₹{{ number_format($addon->price ?? 0, 0) }}</span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between align-items-center">
                <div class="text-muted fs-12">New total will be shown after saving.</div>
                <div>
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" id="btn-save-items"><i class="ri-save-line me-1"></i>Save & Recalculate</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@section('js')
<script>
    var CSRF_TOKEN = '{{ csrf_token() }}';
    var BOOKING_ID = {{ $booking->id }};
    var RESPOND_URL = '{{ route('supervisor.booking.respond', $booking->id) }}';

    function postAction(url, successCb) {
        $.ajax({
            url: url,
            method: 'POST',
            data: { _token: CSRF_TOKEN },
            success: function(resp) {
                showToast(resp.message, 'success');
                if (successCb) successCb(resp);
                setTimeout(() => location.reload(), 1200);
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'Something went wrong.', 'danger');
            }
        });
    }

    // Accept button
    $(document).on('click', '.btn-accept-action', function() {
        $.ajax({
            url: RESPOND_URL,
            method: 'POST',
            data: { status: 'accepted', _token: CSRF_TOKEN },
            success: function(r) { showToast(r.message, 'success'); setTimeout(() => location.reload(), 1200); },
            error: function(xhr) { showToast(xhr.responseJSON?.message || 'Failed.', 'danger'); }
        });
    });

    // Reject button
    $(document).on('click', '.btn-reject-action', function() {
        if (!confirm('Are you sure you want to reject this assignment?')) return;
        $.ajax({
            url: RESPOND_URL,
            method: 'POST',
            data: { status: 'rejected', _token: CSRF_TOKEN },
            success: function(r) { showToast(r.message, 'danger'); setTimeout(() => location.reload(), 1200); },
            error: function(xhr) { showToast(xhr.responseJSON?.message || 'Failed.', 'danger'); }
        });
    });

    // Verify OTP Action
    $(document).on('click', '#btn-verify-otp', function() {
        var url = $(this).data('url');
        var otp = $('#otp-input').val().trim();
        if (!otp) {
            showToast('Please enter the customer OTP.', 'danger');
            return;
        }
        $.ajax({
            url: url,
            method: 'POST',
            data: { otp: otp, _token: CSRF_TOKEN },
            success: function(resp) {
                showToast(resp.message, 'success');
                setTimeout(() => location.reload(), 1200);
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'Verification failed.', 'danger');
            }
        });
    });

    // Upload box photo proof
    $(document).on('submit', '#proof-upload-form', function(e) {
        e.preventDefault();
        var url = $('#btn-upload-proof').data('url');
        var formData = new FormData(this);
        
        $('#btn-upload-proof').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Uploading...');

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(resp) {
                showToast(resp.message, 'success');
                setTimeout(() => location.reload(), 1200);
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'Upload failed.', 'danger');
                $('#btn-upload-proof').prop('disabled', false).html('<i class="ri-upload-cloud-line me-1"></i>Upload & Complete Pickup');
            }
        });
    });

    // Generic action buttons (Start Trip, Start Shifting, Complete)
    $(document).on('click', '.btn-action', function() {
        var url = $(this).data('url');
        var label = $(this).text().trim();
        if (!confirm('Confirm: ' + label + '?')) return;
        postAction(url);
    });

    // Save updated items
    $('#btn-save-items').on('click', function () {
        var items = [];
        $('.item-qty-input').each(function () {
            items.push({ id: $(this).data('item'), quantity: parseInt($(this).val()) || 0 });
        });
        var addons = [];
        $('.addon-check:checked').each(function () {
            addons.push($(this).val());
        });

        $.ajax({
            url: '{{ route('supervisor.booking.updateItems', $booking->id) }}',
            method: 'POST',
            data: { _token: CSRF_TOKEN, items: items, addons: addons },
            success: function(resp) {
                showToast(resp.message, 'success');
                $('#updateItemsModal').modal('hide');
                setTimeout(() => location.reload(), 1200);
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'Failed to update items.', 'danger');
            }
        });
    });

    // Qty ± buttons
    $(document).on('click', '.qty-plus', function() {
        var id = $(this).data('item');
        var inp = $('#qty-' + id);
        inp.val(Math.min(50, parseInt(inp.val() || 0) + 1));
    });
    $(document).on('click', '.qty-minus', function() {
        var id = $(this).data('item');
        var inp = $('#qty-' + id);
        inp.val(Math.max(0, parseInt(inp.val() || 0) - 1));
    });
</script>
@endsection
