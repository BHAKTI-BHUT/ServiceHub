@extends('partials.layouts.master')

@section('title', 'New Booking | Bhanderi Packers and Partner')
@section('sub-title', 'Create Booking')
@section('pagetitle', 'Bookings')

@section('content')
<div class="row g-4">

    {{-- Breadcrumb --}}
    <div class="col-12">
        <nav aria-label="breadcrumb" class="mb-1">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('booking.index') }}">Bookings</a></li>
                <li class="breadcrumb-item active">New Booking</li>
            </ol>
        </nav>
    </div>

    <form id="createBookingForm" action="{{ route('booking.store') }}" method="POST" novalidate>
    @csrf

    {{-- ══════════════════════════════════════════════════════
         LEFT COLUMN — Form Inputs
    ══════════════════════════════════════════════════════ --}}
    <div class="col-xl-8">

        {{-- ── A: Customer & Schedule ── --}}
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <span class="badge bg-primary rounded-circle p-2"><i class="ri-user-line fs-14"></i></span>
                <h6 class="card-title mb-0">Customer & Schedule</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label for="customer_search" class="form-label fw-semibold">Customer <span class="text-danger">*</span></label>
                        <select class="form-select select2-ajax" id="customer_search" name="customer_id" required></select>
                        <div class="invalid-feedback">Please select a customer.</div>
                    </div>
                    <div class="col-md-4">
                        <label for="shifting_date" class="form-label fw-semibold">Shifting Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="shifting_date" name="shifting_date" required>
                    </div>
                    <div class="col-md-4">
                        <label for="shifting_time" class="form-label fw-semibold">Shifting Time <span class="text-danger">*</span></label>
                        <input type="time" class="form-control" id="shifting_time" name="shifting_time" required>
                    </div>
                    <div class="col-md-4">
                        <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="confirmed" selected>Confirmed</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── B: Locations ── --}}
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <span class="badge bg-success rounded-circle p-2"><i class="ri-map-pin-line fs-14"></i></span>
                <h6 class="card-title mb-0">Pickup & Drop Locations</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    {{-- Pickup --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold text-success"><i class="ri-map-pin-user-fill me-1"></i>Pickup Details</label>
                    </div>
                    <div class="col-12">
                        <label for="pickup_location" class="form-label">Pickup Address <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="pickup_location" name="pickup_location" placeholder="Full pickup address" required>
                    </div>
                    <div class="col-md-4">
                        <label for="pickup_contact_name" class="form-label">Contact Name</label>
                        <input type="text" class="form-control" id="pickup_contact_name" name="pickup_contact_name" placeholder="Contact at pickup">
                    </div>
                    <div class="col-md-4">
                        <label for="pickup_contact_mobile" class="form-label">Contact Mobile</label>
                        <input type="text" class="form-control" id="pickup_contact_mobile" name="pickup_contact_mobile" placeholder="10-digit mobile">
                    </div>
                    <div class="col-md-2">
                        <label for="pickup_latitude" class="form-label">Latitude</label>
                        <input type="number" step="any" class="form-control" id="pickup_latitude" name="pickup_latitude" placeholder="23.0225">
                    </div>
                    <div class="col-md-2">
                        <label for="pickup_longitude" class="form-label">Longitude</label>
                        <input type="number" step="any" class="form-control" id="pickup_longitude" name="pickup_longitude" placeholder="72.5714">
                    </div>

                    <div class="col-12"><hr class="my-1"></div>

                    {{-- Drop --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold text-danger"><i class="ri-map-pin-5-fill me-1"></i>Drop Details</label>
                    </div>
                    <div class="col-12">
                        <label for="drop_location" class="form-label">Drop Address <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="drop_location" name="drop_location" placeholder="Full drop address" required>
                    </div>
                    <div class="col-md-4">
                        <label for="drop_contact_name" class="form-label">Contact Name</label>
                        <input type="text" class="form-control" id="drop_contact_name" name="drop_contact_name" placeholder="Contact at destination">
                    </div>
                    <div class="col-md-4">
                        <label for="drop_contact_mobile" class="form-label">Contact Mobile</label>
                        <input type="text" class="form-control" id="drop_contact_mobile" name="drop_contact_mobile" placeholder="10-digit mobile">
                    </div>
                    <div class="col-md-2">
                        <label for="drop_latitude" class="form-label">Latitude</label>
                        <input type="number" step="any" class="form-control" id="drop_latitude" name="drop_latitude" placeholder="23.0338">
                    </div>
                    <div class="col-md-2">
                        <label for="drop_longitude" class="form-label">Longitude</label>
                        <input type="number" step="any" class="form-control" id="drop_longitude" name="drop_longitude" placeholder="72.5850">
                    </div>
                </div>
            </div>
        </div>

        {{-- ── C: Item Selector ── --}}
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-warning rounded-circle p-2"><i class="ri-box-3-line fs-14"></i></span>
                    <h6 class="card-title mb-0">Select Items to Shift</h6>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex gap-2 align-items-center fs-12 text-muted">
                        <span class="badge bg-info-subtle text-info border">Small = 1 pt</span>
                        <span class="badge bg-warning-subtle text-warning border">Medium = 3 pts</span>
                        <span class="badge bg-danger-subtle text-danger border">Large = 5 pts</span>
                    </div>
                    <div class="text-end">
                        <span class="fw-bold text-dark">Score: </span>
                        <span id="totalScoreDisplay" class="badge bg-primary fs-13 px-3">0</span>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @foreach($itemSizes as $size)
                <div class="px-4 {{ $loop->first ? 'pt-3' : 'pt-2' }} pb-2">
                    <p class="text-muted fw-semibold fs-12 mb-2 text-uppercase letter-spacing-1">
                        <i class="ri-checkbox-blank-circle-fill text-primary me-1" style="font-size:8px;"></i>
                        {{ $size->size_name }} Items <span class="badge bg-primary-subtle text-primary ms-1">{{ $size->volume_score }} points each</span>
                    </p>
                    <div class="row g-2">
                        @foreach($size->items as $item)
                        <div class="col-md-4 col-6">
                            <div class="item-card border rounded p-2 d-flex justify-content-between align-items-center" data-item-id="{{ $item->id }}" data-volume="{{ $size->volume_score }}">
                                <span class="fs-12 fw-medium text-truncate me-2" title="{{ $item->item_name }}">{{ $item->item_name }}</span>
                                <div class="qty-control d-flex align-items-center gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-secondary p-0 qty-btn" style="width:22px;height:22px;line-height:1;" data-action="minus" data-item="{{ $item->id }}">−</button>
                                    <span class="qty-display fs-12 fw-bold mx-1" style="min-width:18px;text-align:center;">0</span>
                                    <button type="button" class="btn btn-sm btn-outline-primary p-0 qty-btn" style="width:22px;height:22px;line-height:1;" data-action="plus" data-item="{{ $item->id }}">+</button>
                                </div>
                                <input type="hidden" name="items[{{ $item->id }}][id]" value="{{ $item->id }}" disabled class="qty-input">
                                <input type="hidden" name="items[{{ $item->id }}][quantity]" value="0" disabled class="qty-value">
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @if(!$loop->last)
                    <hr class="mx-4 my-2">
                @endif
                @endforeach
            </div>
        </div>

        {{-- ── D: Add-On Services ── --}}
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-info rounded-circle p-2"><i class="ri-service-line fs-14"></i></span>
                    <h6 class="card-title mb-0">Add-On Services</h6>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($addons as $addon)
                    <div class="col-md-6">
                        <div class="addon-card border rounded p-3 d-flex align-items-center gap-3 cursor-pointer" id="addon-wrap-{{ $addon->id }}">
                            <div class="form-check mb-0">
                                <input class="form-check-input addon-checkbox" type="checkbox"
                                    id="addon_{{ $addon->id }}"
                                    name="addons[]"
                                    value="{{ $addon->id }}"
                                    data-price="{{ $addon->price }}">
                            </div>
                            <label class="form-check-label d-flex justify-content-between align-items-center w-100 cursor-pointer" for="addon_{{ $addon->id }}">
                                <span class="fs-13 fw-medium">{{ $addon->addon_name }}</span>
                                <span class="badge bg-success-subtle text-success fs-12 ms-2 text-nowrap">+₹{{ number_format($addon->price, 0) }}</span>
                            </label>
                        </div>
                    </div>
                    @endforeach

                    {{-- Floor Count --}}
                    <div class="col-12 mt-2">
                        <label for="floors" class="form-label fw-semibold">
                            <i class="ri-building-line me-1"></i>Floors to Carry Without Lift
                            <span class="text-muted fs-12">(total floors at pickup + drop)</span>
                        </label>
                        <div class="d-flex align-items-center gap-3">
                            <input type="number" class="form-control" id="floors" name="floors" min="0" max="20" value="0" style="max-width:120px;">
                            <span class="text-muted fs-12">₹150 per floor will be charged</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════
         RIGHT COLUMN — Live Pricing Panel
    ══════════════════════════════════════════════════════ --}}
    <div class="col-xl-4">
        <div class="card sticky-top" style="top:80px;">
            <div class="card-header bg-primary text-white d-flex align-items-center gap-2">
                <i class="ri-price-tag-3-line fs-18"></i>
                <h6 class="card-title mb-0 text-white">Live Price Calculator</h6>
            </div>
            <div class="card-body p-3">

                {{-- Volume Score Bar --}}
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fs-12 text-muted">Volume Score</span>
                        <span class="fs-12 fw-bold" id="scoreLabel">0 / 310 pts</span>
                    </div>
                    <div class="progress" style="height:8px;">
                        <div class="progress-bar bg-primary" id="scoreBar" role="progressbar" style="width:0%"></div>
                    </div>
                </div>

                {{-- Category Detected --}}
                <div class="mb-3 p-3 bg-light rounded">
                    <div class="fs-12 text-muted mb-1">Auto-Detected Category</div>
                    <div id="categoryDetected" class="fw-bold text-primary fs-14">—</div>
                    <div class="fs-12 text-muted mt-1"><i class="ri-truck-line me-1"></i><span id="vehicleDetected">No vehicle assigned</span></div>
                </div>

                {{-- Survey Required Alert --}}
                <div id="surveyAlert" class="alert alert-danger d-none p-2 fs-12" role="alert">
                    <i class="ri-error-warning-line me-1"></i>
                    <strong>Survey Required!</strong><br>
                    Volume is too large for auto-quote. A Bhanderi Packers representative will conduct a free physical survey.
                </div>

                <hr class="my-2">

                {{-- Price Breakdown --}}
                <div id="pricePanel">
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="fs-13 text-muted">Base Fare</span>
                        <span class="fs-13 fw-semibold" id="baseFareVal">₹0</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="fs-13 text-muted">Distance Charges</span>
                        <span class="fs-13 fw-semibold" id="distanceVal">₹0</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="fs-13 text-muted">Add-On Services</span>
                        <span class="fs-13 fw-semibold" id="addonVal">₹0</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="fs-13 text-muted">Floor Charges</span>
                        <span class="fs-13 fw-semibold" id="floorVal">₹0</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom" id="weekendRow" style="display:none!important;">
                        <span class="fs-13 text-warning">Weekend Surcharge</span>
                        <span class="fs-13 fw-semibold text-warning" id="weekendVal">₹0</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom" id="monthEndRow" style="display:none!important;">
                        <span class="fs-13 text-warning">Month-End Surcharge</span>
                        <span class="fs-13 fw-semibold text-warning" id="monthEndVal">₹0</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 mt-1">
                        <span class="fs-15 fw-bold">Total Amount</span>
                        <span class="fs-16 fw-bold text-primary" id="totalAmountVal">₹0</span>
                    </div>
                    <div class="d-flex justify-content-between text-muted">
                        <span class="fs-12">Advance (20%)</span>
                        <span class="fs-12" id="advanceVal">₹0</span>
                    </div>
                    <div class="d-flex justify-content-between text-muted">
                        <span class="fs-12">Distance</span>
                        <span class="fs-12" id="distanceKmVal">0 km</span>
                    </div>
                </div>

                {{-- Calculate Button --}}
                <button type="button" id="calcPriceBtn" class="btn btn-primary w-100 mt-3">
                    <i class="ri-refresh-line me-1"></i> Calculate Price
                </button>
                <div id="calcSpinner" class="text-center mt-2 d-none">
                    <div class="spinner-border spinner-border-sm text-primary"></div>
                    <span class="fs-12 ms-1">Calculating...</span>
                </div>

                <hr class="my-3">

                {{-- Submit Button --}}
                <button type="submit" id="submitBookingBtn" class="btn btn-success w-100 btn-lg">
                    <i class="ri-save-line me-1"></i> Save Booking
                </button>
                <a href="{{ route('booking.index') }}" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>

            </div>
        </div>
    </div>

    </form>
</div>
@endsection

@section('js')
<style>
    .item-card { transition: all 0.2s ease; cursor: default; }
    .item-card.has-qty { background: #f0f7ff; border-color: #0d6efd !important; }
    .addon-card { transition: all 0.2s ease; cursor: pointer; }
    .addon-card.selected { background: #f0fff4; border-color: #198754 !important; }
</style>
<script>
$(document).ready(function () {

    // ── Init ─────────────────────────────────────────────────────────────
    var today = new Date().toISOString().split('T')[0];
    $('#shifting_date').attr('min', today);

    initSelect2Ajax('#customer_search', '{{ route('booking.search-customers') }}', 'Search by Name, Mobile or Email...');

    // ── Volume Score Tracker ─────────────────────────────────────────────
    var itemQtys = {};   // { itemId: qty }

    function getTotalScore() {
        var total = 0;
        $('.item-card').each(function () {
            var id  = $(this).data('item-id');
            var vol = parseInt($(this).data('volume')) || 0;
            var qty = itemQtys[id] || 0;
            total += vol * qty;
        });
        return total;
    }

    function updateScoreDisplay() {
        var score = getTotalScore();
        var max   = 310;
        var pct   = Math.min(100, Math.round(score / max * 100));

        $('#totalScoreDisplay').text(score);
        $('#scoreLabel').text(score + ' / ' + max + ' pts');
        $('#scoreBar').css('width', pct + '%');

        if (score > max) {
            $('#scoreBar').removeClass('bg-primary bg-warning').addClass('bg-danger');
            $('#surveyAlert').removeClass('d-none');
            $('#categoryDetected').text('Survey Required');
            $('#vehicleDetected').text('Physical survey needed');
        } else {
            $('#scoreBar').removeClass('bg-danger bg-warning').addClass('bg-primary');
            $('#surveyAlert').addClass('d-none');
        }
    }

    // ── Quantity Buttons ─────────────────────────────────────────────────
    $(document).on('click', '.qty-btn', function (e) {
        e.preventDefault();
        var itemId = $(this).data('item');
        var action = $(this).data('action');
        var card   = $(this).closest('.item-card');
        var curr   = itemQtys[itemId] || 0;

        if (action === 'plus') curr = Math.min(curr + 1, 50);
        if (action === 'minus') curr = Math.max(curr - 1, 0);

        itemQtys[itemId] = curr;
        card.find('.qty-display').text(curr);

        // Enable / disable hidden inputs so they're submitted
        var qtyInput = card.find('.qty-input');
        var qtyValue = card.find('.qty-value');
        if (curr > 0) {
            card.addClass('has-qty');
            qtyInput.prop('disabled', false);
            qtyValue.prop('disabled', false).val(curr);
        } else {
            card.removeClass('has-qty');
            qtyInput.prop('disabled', true);
            qtyValue.prop('disabled', true).val(0);
        }

        updateScoreDisplay();
    });

    // ── Add-On Card Toggle ───────────────────────────────────────────────
    $(document).on('change', '.addon-checkbox', function () {
        var wrap = $(this).closest('.addon-card');
        if ($(this).is(':checked')) wrap.addClass('selected');
        else wrap.removeClass('selected');
    });

    $(document).on('click', '.addon-card', function (e) {
        if (!$(e.target).is('input')) {
            $(this).find('.addon-checkbox').trigger('click');
        }
    });

    // ── Build AJAX payload ───────────────────────────────────────────────
    function buildPricingPayload() {
        var items = [];
        $.each(itemQtys, function (id, qty) {
            if (qty > 0) items.push({ id: id, quantity: qty });
        });

        var addons = [];
        $('.addon-checkbox:checked').each(function () {
            addons.push(parseInt($(this).val()));
        });

        return {
            items:             items,
            addons:            addons,
            pickup_latitude:   $('#pickup_latitude').val()  || null,
            pickup_longitude:  $('#pickup_longitude').val() || null,
            drop_latitude:     $('#drop_latitude').val()    || null,
            drop_longitude:    $('#drop_longitude').val()   || null,
            shifting_date:     $('#shifting_date').val()    || null,
            floors:            parseInt($('#floors').val()) || 0,
            _token:            '{{ csrf_token() }}'
        };
    }

    // ── Format currency ──────────────────────────────────────────────────
    function fmt(n) {
        return '₹' + parseFloat(n || 0).toLocaleString('en-IN', { minimumFractionDigits: 0 });
    }

    // ── Calculate Price Button ───────────────────────────────────────────
    $('#calcPriceBtn').on('click', function () {
        $('#calcSpinner').removeClass('d-none');
        $(this).prop('disabled', true);

        $.ajax({
            url:  '{{ route('admin.booking.ajax-pricing') }}',
            type: 'POST',
            data: JSON.stringify(buildPricingPayload()),
            contentType: 'application/json',
            success: function (data) {
                if (data.survey_required) {
                    $('#categoryDetected').text('Survey Required');
                    $('#vehicleDetected').text('Physical survey needed');
                    $('#surveyAlert').removeClass('d-none');
                    ['baseFareVal','distanceVal','addonVal','floorVal','weekendVal','monthEndVal','totalAmountVal','advanceVal']
                        .forEach(id => $('#'+id).text('—'));
                    return;
                }

                $('#surveyAlert').addClass('d-none');
                $('#categoryDetected').text(data.category_name || '—');
                $('#vehicleDetected').text(data.vehicle_name || 'No vehicle assigned');

                $('#baseFareVal').text(fmt(data.base_fare));
                $('#distanceVal').text(fmt(data.distance_charges));
                $('#addonVal').text(fmt(data.addon_charges));
                $('#floorVal').text(fmt(data.floor_charges));
                $('#totalAmountVal').text(fmt(data.total_amount));
                $('#advanceVal').text(fmt(data.total_amount * 0.20));
                $('#distanceKmVal').text((data.total_distance_km || 0) + ' km');

                if (data.weekend_charges > 0) {
                    $('#weekendVal').text(fmt(data.weekend_charges));
                    $('#weekendRow').show();
                } else { $('#weekendRow').hide(); }

                if (data.month_end_charges > 0) {
                    $('#monthEndVal').text(fmt(data.month_end_charges));
                    $('#monthEndRow').show();
                } else { $('#monthEndRow').hide(); }
            },
            error: function () {
                toastr.error('Could not calculate pricing. Please check the form fields.');
            },
            complete: function () {
                $('#calcSpinner').addClass('d-none');
                $('#calcPriceBtn').prop('disabled', false);
            }
        });
    });

    // ── Auto-calculate on date / floor change ────────────────────────────
    $('#shifting_date, #floors').on('change', function () {
        if (getTotalScore() > 0) $('#calcPriceBtn').trigger('click');
    });

    // ── Form Submission ──────────────────────────────────────────────────
    $('#createBookingForm').on('submit', function (e) {
        e.preventDefault();

        var $btn = $('#submitBookingBtn');
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');

        $.ajax({
            url:  $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function (res) {
                if (res.survey_required) {
                    toastr.warning(res.message, 'Survey Required');
                    $btn.prop('disabled', false).html('<i class="ri-save-line me-1"></i> Save Booking');
                    return;
                }
                toastr.success(res.message || 'Booking created!');
                setTimeout(() => window.location.href = '{{ route('booking.index') }}', 1000);
            },
            error: function (xhr) {
                $btn.prop('disabled', false).html('<i class="ri-save-line me-1"></i> Save Booking');
                var res = xhr.responseJSON || {};
                if (res.errors) {
                    $.each(res.errors, function (field, msgs) {
                        toastr.error(msgs[0], 'Validation Error');
                    });
                } else if (res.survey_required) {
                    toastr.warning(res.message, 'Survey Required');
                } else {
                    toastr.error(res.message || 'Something went wrong!');
                }
            }
        });
    });

});
</script>
@endsection
