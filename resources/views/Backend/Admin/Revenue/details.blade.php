@extends('partials.layouts.master')
@section('title', 'Booking Details | ServiceHub')
@section('sub-title', 'Revenue')
@section('pagetitle', 'Booking Details')

@section('css')
<style>
/* ── Main Container ─────────────────────────────────────── */
.detail-wrapper { width: 100%; }

/* ── Hero Header Card ───────────────────────────────────── */
.hero-card {
    background: linear-gradient(135deg, #1e3a5f 0%, #0f6ebc 100%);
    border-radius: 0;
    padding: 32px 36px;
    color: #fff;
    position: relative;
    overflow: hidden;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(15,110,188,.2);
}
.hero-card::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 220px; height: 220px;
    border-radius: 50%;
    background: rgba(255,255,255,.07);
}
.hero-card::after {
    content: '';
    position: absolute;
    bottom: -40px; left: -30px;
    width: 160px; height: 160px;
    border-radius: 50%;
    background: rgba(255,255,255,.05);
}
.hero-booking-no {
    font-size: 28px;
    font-weight: 800;
    letter-spacing: 1px;
    margin-bottom: 4px;
}
.hero-badge {
    display: inline-block;
    padding: 4px 14px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 600;
    background: rgba(255,255,255,.18);
    backdrop-filter: blur(6px);
    letter-spacing: .5px;
}
.hero-meta { font-size: 13px; opacity: .82; margin-top: 6px; }
.hero-meta i { margin-right: 4px; }
.hero-actions { display: flex; gap: 10px; flex-wrap: wrap; justify-content: flex-end; }
.hero-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 20px;
    border-radius: 4px;
    font-weight: 600; font-size: 13px;
    border: 2px solid rgba(255,255,255,.4);
    background: rgba(255,255,255,.12);
    color: #fff;
    text-decoration: none;
    transition: all .2s;
    backdrop-filter: blur(4px);
}
.hero-btn:hover { background: rgba(255,255,255,.22); color: #fff; border-color: rgba(255,255,255,.7); }
.hero-btn.solid { background: #fff; color: #0f6ebc; border-color: #fff; }
.hero-btn.solid:hover { background: #e8f4ff; }

/* ── Info Grid Cards ────────────────────────────────────── */
.info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px,1fr)); gap: 20px; margin-bottom: 24px; }
.info-card {
    background: #fff;
    border-radius: 0;
    padding: 22px 24px;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
    border: 1px solid #e9ecef;
}
.info-card-title {
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 1px; color: #8a9bb0; margin-bottom: 14px;
    display: flex; align-items: center; gap: 6px;
}
.info-row { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; font-size: 13px; }
.info-row:last-child { margin-bottom: 0; }
.info-label { color: #6c757d; font-weight: 500; }
.info-value { color: #1a2b45; font-weight: 600; text-align: right; max-width: 60%; }

/* ── Payment Summary Row ────────────────────────────────── */
.pay-summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); gap: 16px; margin-bottom: 24px; }
.pay-tile {
    border-radius: 0;
    padding: 18px 20px;
    display: flex; align-items: center; gap: 14px;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
    border: 1px solid #e9ecef;
}
.pay-tile.total   { background: linear-gradient(135deg,#eef9f0,#d6f5dd); }
.pay-tile.reg     { background: linear-gradient(135deg,#eef3ff,#d9e5ff); }
.pay-tile.remain  { background: linear-gradient(135deg,#fff8ee,#ffe9c8); }
.pay-tile.paid    { background: linear-gradient(135deg,#eef9f0,#c4f0cd); }
.pay-tile-icon { width: 44px; height: 44px; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
.pay-tile.total  .pay-tile-icon { background:#28a745; color:#fff; }
.pay-tile.reg    .pay-tile-icon { background:#4361ee; color:#fff; }
.pay-tile.remain .pay-tile-icon { background:#f5a623; color:#fff; }
.pay-tile.paid   .pay-tile-icon { background:#20c997; color:#fff; }
.pay-tile-label { font-size: 11px; color: #6c757d; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 2px; }
.pay-tile-value { font-size: 20px; font-weight: 800; color: #1a2b45; }

/* ── Horizontal Timeline ────────────────────────────────── */
.htl-section { background: #fff; border: 1px solid #e9ecef; padding: 28px 30px; margin-bottom: 24px; box-shadow: 0 1px 4px rgba(0,0,0,.06); }
.htl-track {
    display: flex;
    align-items: flex-start;
    position: relative;
    overflow-x: auto;
    padding-bottom: 4px;
}
.htl-track::before {
    content: '';
    position: absolute;
    top: 22px;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(to right, #4361ee, #f5a623, #28a745);
    z-index: 0;
}
.htl-step {
    flex: 1;
    min-width: 160px;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 1;
    padding: 0 8px;
}
.htl-dot {
    width: 44px; height: 44px;
    border-radius: 50%;
    border: 3px solid #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    margin-bottom: 12px;
    box-shadow: 0 0 0 3px;
    flex-shrink: 0;
}
.htl-dot.primary { background:#4361ee; box-shadow: 0 0 0 3px #4361ee40; }
.htl-dot.success { background:#28a745; box-shadow: 0 0 0 3px #28a74540; }
.htl-dot.warning { background:#f5a623; box-shadow: 0 0 0 3px #f5a62340; }
.htl-dot.danger  { background:#dc3545; box-shadow: 0 0 0 3px #dc354540; }
.htl-dot i { color: #fff; font-size: 18px; }
.htl-body { text-align: center; }
.htl-title { font-size: 13px; font-weight: 700; color: #1a2b45; margin-bottom: 3px; }
.htl-sub { font-size: 11px; color: #6c757d; line-height: 1.4; margin-bottom: 4px; }
.htl-amount { font-size: 15px; font-weight: 800; color: #1a2b45; }
.htl-badge { display: inline-block; padding: 2px 10px; border-radius: 3px; font-size: 11px; font-weight: 600; margin-top: 4px; }
.htl-badge.paid-badge { background: #d4edda; color: #155724; }
.htl-badge.pending-badge { background: #fff3cd; color: #856404; }

/* ── Route Card ─────────────────────────────────────────── */
.route-card { background: #fff; border: 1px solid #e9ecef; padding: 22px 24px; box-shadow: 0 1px 4px rgba(0,0,0,.06); }
.route-path { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.route-point { display: flex; align-items: center; gap: 8px; }
.route-dot { width: 12px; height: 12px; border-radius: 50%; }
.route-dot.pickup { background: #28a745; }
.route-dot.drop   { background: #dc3545; }
.route-location { font-size: 13px; color: #1a2b45; font-weight: 600; }
.route-arrow { color: #adb5bd; font-size: 18px; }
.route-meta { display: flex; gap: 20px; flex-wrap: wrap; margin-top: 12px; padding-top: 12px; border-top: 1px solid #e9ecef; }
.route-meta-item { font-size: 12px; color: #6c757d; display: flex; align-items: center; gap: 5px; font-weight: 500; }
.route-meta-item span { color: #1a2b45; font-weight: 700; }
</style>
@endsection

@section('content')
@php
    $regPaid      = $booking->registration_payment_status === 'paid';
    $remainPaid   = $booking->remaining_payment_status === 'paid';
    $defaultRegFee = \App\Models\PricingSetting::where('key', 'registration_fee')->value('value') ?? 500;
    $remainingAmt = $booking->amount - ($booking->registration_charge ?? $defaultRegFee);
    $statusColors = [
        'pending'    => 'warning',
        'confirmed'  => 'primary',
        'completed'  => 'success',
        'cancelled'  => 'danger',
    ];
    $statusColor = $statusColors[$booking->status] ?? 'secondary';
@endphp

<div class="detail-wrapper">

    {{-- ── Hero Header ── --}}
    <div class="hero-card">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div class="hero-booking-no">🚚 {{ $booking->booking_number }}</div>
                <div class="mb-2">
                    <span class="hero-badge">{{ ucfirst($booking->status) }}</span>
                </div>
                <div class="hero-meta">
                    <i class="ri-user-3-line"></i> {{ $booking->customer->name ?? 'N/A' }}
                    &nbsp;&nbsp;
                    <i class="ri-phone-line"></i> {{ $booking->customer->mobile ?? '—' }}
                    &nbsp;&nbsp;
                    <i class="ri-calendar-line"></i> Shifting: {{ date('d M Y', strtotime($booking->shifting_date)) }}
                    @if($booking->shifting_time)
                        at {{ date('h:i A', strtotime($booking->shifting_time)) }}
                    @endif
                </div>
            </div>
            <div class="hero-actions">
                @if(!$remainPaid)
                    <button class="hero-btn solid btn-record-payment" data-url="{{ route('admin.booking.record-payment', $booking->id) }}" style="background-color: #28a745; border-color: #28a745; cursor: pointer; color: #fff;">
                        <i class="ri-check-line"></i> Record Payment Paid
                    </button>
                @endif
                <a href="{{ route('admin.revenue.invoice', $booking->id) }}" target="_blank" class="hero-btn solid">
                    <i class="ri-file-list-3-line"></i> Print Invoice
                </a>
                <a href="{{ route('admin.revenue') }}" class="hero-btn">
                    <i class="ri-arrow-left-line"></i> Back
                </a>
            </div>
        </div>
    </div>

    {{-- ── Payment Summary Tiles ── --}}
    <div class="pay-summary">
        <div class="pay-tile total">
            <div class="pay-tile-icon"><i class="ri-money-rupee-circle-fill"></i></div>
            <div>
                <div class="pay-tile-label">Total Amount</div>
                <div class="pay-tile-value">₹{{ number_format($booking->amount, 2) }}</div>
            </div>
        </div>
        <div class="pay-tile reg">
            <div class="pay-tile-icon"><i class="ri-shield-check-fill"></i></div>
            <div>
                <div class="pay-tile-label">Registration Fee</div>
                <div class="pay-tile-value">₹{{ number_format($booking->registration_charge ?? $defaultRegFee, 2) }}</div>
            </div>
        </div>
        <div class="pay-tile remain">
            <div class="pay-tile-icon"><i class="ri-time-fill"></i></div>
            <div>
                <div class="pay-tile-label">Remaining Amount</div>
                <div class="pay-tile-value">₹{{ $remainPaid ? '0.00' : number_format($remainingAmt, 2) }}</div>
            </div>
        </div>
        <div class="pay-tile paid">
            <div class="pay-tile-icon"><i class="ri-check-double-fill"></i></div>
            <div>
                <div class="pay-tile-label">Payment Status</div>
                <div class="pay-tile-value" style="font-size:14px; padding-top:4px;">
                    @if($remainPaid)
                        <span style="color:#1a7a3e;">✅ Fully Paid</span>
                    @else
                        <span style="color:#d97706;">⏳ Pending</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="pay-tile" style="background:#fffbeb; border:1px solid #fef3c7;">
            <div class="pay-tile-icon" style="color:#d97706;"><i class="ri-key-2-fill"></i></div>
            <div>
                <div class="pay-tile-label" style="color:#b45309;">Shifting OTP</div>
                <div class="pay-tile-value" style="color:#b45309; font-family:monospace; letter-spacing:1px;">{{ $booking->pickup_otp ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    {{-- ── Horizontal Payment Timeline ── --}}
    <div class="htl-section">
        <div class="section-heading" style="font-size:15px;font-weight:700;color:#1a2b45;margin-bottom:24px;display:flex;align-items:center;gap:8px;">
            <i class="ri-history-line text-primary"></i> Payment History & Timeline
        </div>
        <div class="htl-track">

            {{-- Step 1: Booking Created --}}
            <div class="htl-step">
                <div class="htl-dot primary"><i class="ri-add-circle-line"></i></div>
                <div class="htl-body">
                    <div class="htl-title">📋 Booking Created</div>
                    <div class="htl-sub">{{ $booking->created_at->format('d M Y') }}<br>{{ $booking->created_at->format('h:i A') }}</div>
                </div>
            </div>

            {{-- Step 2: Registration Fee --}}
            <div class="htl-step">
                <div class="htl-dot {{ $regPaid ? 'success' : 'warning' }}"><i class="ri-money-rupee-circle-line"></i></div>
                <div class="htl-body">
                    <div class="htl-title">💳 Registration Fee</div>
                    <div class="htl-amount">₹{{ number_format($booking->registration_charge ?? $defaultRegFee, 2) }}</div>
                    @if($regPaid)
                        <div class="htl-sub">{{ $booking->updated_at->format('d M Y, h:i A') }}</div>
                        @if($booking->registration_payment_id)
                            <div class="htl-sub" style="word-break:break-all;">Txn: {{ $booking->registration_payment_id }}</div>
                        @endif
                    @else
                        <div class="htl-sub">Not yet paid</div>
                    @endif
                    <span class="htl-badge {{ $regPaid ? 'paid-badge' : 'pending-badge' }}">{{ $regPaid ? 'Paid' : 'Pending' }}</span>
                </div>
            </div>

            {{-- Step 3: Shifting Scheduled --}}
            <div class="htl-step">
                <div class="htl-dot primary"><i class="ri-truck-line"></i></div>
                <div class="htl-body">
                    <div class="htl-title">🚛 Shifting Date</div>
                    <div class="htl-sub">{{ date('d M Y', strtotime($booking->shifting_date)) }}<br>
                        @if($booking->shifting_time){{ date('h:i A', strtotime($booking->shifting_time)) }}@endif
                    </div>
                </div>
            </div>

            {{-- Step 4: Remaining Balance --}}
            <div class="htl-step">
                <div class="htl-dot {{ $remainPaid ? 'success' : 'danger' }}"><i class="ri-bank-card-line"></i></div>
                <div class="htl-body">
                    <div class="htl-title">🏦 Remaining Balance</div>
                    <div class="htl-amount">₹{{ $remainPaid ? '0.00' : number_format($remainingAmt, 2) }}</div>
                    @if($remainPaid)
                        <div class="htl-sub">{{ $booking->updated_at->format('d M Y, h:i A') }}</div>
                    @else
                        <div class="htl-sub">⚠️ Payment pending</div>
                    @endif
                    <span class="htl-badge {{ $remainPaid ? 'paid-badge' : 'pending-badge' }}">{{ $remainPaid ? 'Paid' : 'Pending' }}</span>
                </div>
            </div>

            {{-- Step 5: Completed --}}
            @if($booking->status === 'completed')
            <div class="htl-step">
                <div class="htl-dot success"><i class="ri-check-double-line"></i></div>
                <div class="htl-body">
                    <div class="htl-title">✅ Completed</div>
                    <div class="htl-sub">{{ $booking->updated_at->format('d M Y') }}</div>
                </div>
            </div>
            @endif

        </div>
    </div>

    {{-- ── Booking Info Row (3 cards) ── --}}
    <div class="row g-4">

        {{-- Route --}}
        <div class="col-lg-4">
            <div class="route-card h-100 mb-0">
                <div class="info-card-title" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#8a9bb0;margin-bottom:14px;display:flex;align-items:center;gap:6px;">
                    <i class="ri-map-pin-line text-danger"></i> Shifting Route
                </div>
                <div class="route-path">
                    <div class="route-point">
                        <div class="route-dot pickup"></div>
                        <div class="route-location">{{ $booking->pickup_location }}</div>
                    </div>
                    <div class="route-arrow">→</div>
                    <div class="route-point">
                        <div class="route-dot drop"></div>
                        <div class="route-location">{{ $booking->drop_location }}</div>
                    </div>
                </div>
                <div class="route-meta">
                    @if($booking->total_distance)
                    <div class="route-meta-item"><i class="ri-road-map-line"></i> Distance: <span>{{ $booking->total_distance }} km</span></div>
                    @endif
                    @if($booking->floors)
                    <div class="route-meta-item"><i class="ri-building-line"></i> Floors: <span>{{ $booking->floors }}</span></div>
                    @endif
                    @if($booking->vehicle)
                    <div class="route-meta-item"><i class="ri-truck-line"></i> Vehicle: <span>{{ $booking->vehicle->vehicle_name ?? '—' }}</span></div>
                    @endif
                    @if($booking->category)
                    <div class="route-meta-item"><i class="ri-list-check"></i> Category: <span>{{ $booking->category->category_name ?? '—' }}</span></div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Pricing Breakdown --}}
        <div class="col-lg-4">
            <div class="info-card h-100">
                <div class="info-card-title"><i class="ri-price-tag-3-line"></i> Pricing Breakdown</div>
                @if($booking->base_fare)
                <div class="info-row"><span class="info-label">Base Fare</span><span class="info-value">₹{{ number_format($booking->base_fare, 2) }}</span></div>
                @endif
                @if($booking->distance_charges)
                <div class="info-row"><span class="info-label">Distance Charges</span><span class="info-value">₹{{ number_format($booking->distance_charges, 2) }}</span></div>
                @endif
                @if($booking->floor_charges)
                <div class="info-row"><span class="info-label">Floor Charges</span><span class="info-value">₹{{ number_format($booking->floor_charges, 2) }}</span></div>
                @endif
                @if($booking->loading_charge)
                <div class="info-row"><span class="info-label">Loading Charge</span><span class="info-value">₹{{ number_format($booking->loading_charge, 2) }}</span></div>
                @endif
                @if($booking->unloading_charge)
                <div class="info-row"><span class="info-label">Unloading Charge</span><span class="info-value">₹{{ number_format($booking->unloading_charge, 2) }}</span></div>
                @endif
                @if($booking->packing_charge)
                <div class="info-row"><span class="info-label">Packing Charge</span><span class="info-value">₹{{ number_format($booking->packing_charge, 2) }}</span></div>
                @endif
                @if($booking->addon_charges)
                <div class="info-row"><span class="info-label">Add-On Charges</span><span class="info-value">₹{{ number_format($booking->addon_charges, 2) }}</span></div>
                @endif
                @if($booking->weekend_charges)
                <div class="info-row"><span class="info-label">Weekend Charges</span><span class="info-value">₹{{ number_format($booking->weekend_charges, 2) }}</span></div>
                @endif
                <div class="info-row" style="border-top:1px solid #e9ecef;padding-top:10px;margin-top:4px;">
                    <span class="info-label" style="font-weight:700;color:#1a2b45;">Grand Total</span>
                    <span class="info-value" style="font-size:16px;color:#0f6ebc;">₹{{ number_format($booking->amount, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Contact Info --}}
        <div class="col-lg-4">
            <div class="info-card h-100">
                <div class="info-card-title"><i class="ri-contacts-line"></i> Contact Details</div>
                @if($booking->pickup_contact_name)
                <div class="info-row">
                    <span class="info-label">Pickup Contact</span>
                    <span class="info-value">{{ $booking->pickup_contact_name }}<br><small>{{ $booking->pickup_contact_mobile }}</small></span>
                </div>
                @endif
                @if($booking->drop_contact_name)
                <div class="info-row">
                    <span class="info-label">Drop Contact</span>
                    <span class="info-value">{{ $booking->drop_contact_name }}<br><small>{{ $booking->drop_contact_mobile }}</small></span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Customer Email</span>
                    <span class="info-value">{{ $booking->customer->email ?? '—' }}</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $(document).on('click', '.btn-record-payment', function() {
            var url = $(this).data('url');
            if (!confirm('Are you sure you want to record the remaining payment as paid directly to admin?')) return;
            $.ajax({
                url: url,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(resp) {
                    showToast(resp.message, 'success');
                    setTimeout(() => location.reload(), 1200);
                },
                error: function(xhr) {
                    showToast(xhr.responseJSON?.message || 'Failed to record payment.', 'danger');
                }
            });
        });
    });
</script>
@endsection
