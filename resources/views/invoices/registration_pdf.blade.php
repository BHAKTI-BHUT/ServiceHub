@php
    $defaultRegFee = \App\Models\PricingSetting::where('key', 'registration_fee')->value('value') ?? 500;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration Invoice - {{ $booking->booking_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px; color: #333; background: #fff; }
        .invoice-container { max-width: 800px; margin: 0 auto; padding: 40px; }

        /* Header */
        .invoice-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #4361ee; padding-bottom: 20px; margin-bottom: 30px; }
        .company-info h1 { font-size: 28px; font-weight: 700; color: #4361ee; margin-bottom: 5px; }
        .company-info p { font-size: 12px; color: #666; line-height: 1.6; }
        .invoice-title { text-align: right; }
        .invoice-title h2 { font-size: 24px; font-weight: 700; color: #333; text-transform: uppercase; letter-spacing: 2px; }
        .invoice-title .invoice-number { font-size: 13px; color: #666; margin-top: 5px; }
        .invoice-title .invoice-date { font-size: 12px; color: #999; margin-top: 3px; }

        /* Status Badge */
        .status-paid { display: inline-block; background: #d1fae5; color: #065f46; padding: 4px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-top: 8px; }

        /* Customer Info */
        .customer-section { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .customer-section .col { width: 48%; }
        .customer-section h4 { font-size: 11px; text-transform: uppercase; color: #999; letter-spacing: 1px; margin-bottom: 8px; font-weight: 600; }
        .customer-section p { font-size: 13px; color: #333; line-height: 1.8; }

        /* Table */
        .invoice-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .invoice-table thead th { background: #4361ee; color: #fff; padding: 12px 16px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; text-align: left; }
        .invoice-table thead th:last-child { text-align: right; }
        .invoice-table tbody td { padding: 14px 16px; border-bottom: 1px solid #eee; font-size: 13px; }
        .invoice-table tbody td:last-child { text-align: right; font-weight: 600; }
        .invoice-table tbody tr:hover { background: #f8f9ff; }

        /* Totals */
        .totals-section { display: flex; justify-content: flex-end; margin-bottom: 30px; }
        .totals-table { width: 320px; }
        .totals-table .row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 13px; border-bottom: 1px solid #f0f0f0; }
        .totals-table .row.total { border-top: 2px solid #4361ee; border-bottom: none; padding-top: 12px; margin-top: 5px; }
        .totals-table .row.total span { font-size: 18px; font-weight: 700; color: #4361ee; }
        .totals-table .row .label { color: #666; }
        .totals-table .row .value { font-weight: 600; color: #333; }

        /* Payment Info */
        .payment-info { background: #f8f9ff; border: 1px solid #e8ecff; border-radius: 8px; padding: 20px; margin-bottom: 30px; }
        .payment-info h4 { font-size: 13px; font-weight: 700; color: #4361ee; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
        .payment-info .info-row { display: flex; justify-content: space-between; padding: 5px 0; font-size: 12px; }
        .payment-info .info-row .label { color: #666; }
        .payment-info .info-row .value { font-weight: 600; color: #333; font-family: 'Courier New', monospace; }

        /* Footer */
        .invoice-footer { text-align: center; padding-top: 20px; border-top: 1px solid #eee; }
        .invoice-footer p { font-size: 11px; color: #999; line-height: 1.8; }
        .invoice-footer .thank-you { font-size: 16px; color: #4361ee; font-weight: 600; margin-bottom: 8px; }

        /* Print Styles */
        @media print {
            body { background: #fff; }
            .invoice-container { padding: 20px; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Print Button -->
        <div class="no-print" style="text-align: right; margin-bottom: 20px;">
            <button onclick="window.print()" style="background: #4361ee; color: #fff; border: none; padding: 10px 24px; border-radius: 6px; font-size: 14px; cursor: pointer; font-weight: 600;">
                <span style="margin-right: 6px;">🖨️</span> Print Invoice
            </button>
        </div>

        <!-- Header -->
        <div class="invoice-header">
            <div class="company-info">
                <h1>Bhandari Packers & Movers</h1>
                <p>Professional Shifting Services<br>
                   info@bhandaripackersandmovers.in</p>
            </div>
            <div class="invoice-title">
                <h2>Invoice</h2>
                <div class="invoice-number">REG-INV-{{ $booking->booking_number }}</div>
                <div class="invoice-date">Date: {{ $booking->updated_at->format('d M Y, h:i A') }}</div>
                <div class="status-paid">✓ PAID</div>
            </div>
        </div>

        <!-- Customer & Booking Info -->
        <div class="customer-section">
            <div class="col">
                <h4>Billed To</h4>
                <p>
                    <strong>{{ $booking->customer->name ?? 'N/A' }}</strong><br>
                    {{ $booking->customer->email ?? '' }}<br>
                    {{ $booking->customer->mobile ?? '' }}
                </p>
            </div>
            <div class="col" style="text-align: right;">
                <h4>Booking Reference</h4>
                <p>
                    <strong>{{ $booking->booking_number }}</strong><br>
                    Shifting Date: {{ date('d M Y', strtotime($booking->shifting_date)) }}<br>
                    From: {{ \Illuminate\Support\Str::limit($booking->pickup_location, 40) }}<br>
                    To: {{ \Illuminate\Support\Str::limit($booking->drop_location, 40) }}
                </p>
            </div>
        </div>

        <!-- Invoice Table -->
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Description</th>
                    <th>Amount (₹)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>
                        <strong>Compulsory Registration & Booking Fee</strong><br>
                        <span style="font-size: 11px; color: #999;">One-time non-refundable registration charge for booking confirmation</span>
                    </td>
                    <td>₹{{ number_format($booking->registration_charge ?? $defaultRegFee, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <div class="totals-table">
                <div class="row">
                    <span class="label">Subtotal</span>
                    <span class="value">₹{{ number_format($booking->registration_charge ?? $defaultRegFee, 2) }}</span>
                </div>
                <div class="row total">
                    <span>Total Paid</span>
                    <span>₹{{ number_format($booking->registration_charge ?? $defaultRegFee, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="payment-info">
            <h4>Payment Details</h4>
            <div class="info-row">
                <span class="label">Payment Mode</span>
                <span class="value">Razorpay (Online)</span>
            </div>
            <div class="info-row">
                <span class="label">Transaction ID</span>
                <span class="value">{{ $booking->registration_payment_id }}</span>
            </div>
            <div class="info-row">
                <span class="label">Order ID</span>
                <span class="value">{{ $booking->registration_order_id }}</span>
            </div>
            <div class="info-row">
                <span class="label">Payment Status</span>
                <span class="value" style="color: #065f46;">PAID</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="invoice-footer">
            <p class="thank-you">Thank you for choosing Bhandari Packers & Movers!</p>
            <p>This is a computer-generated invoice and does not require a physical signature.<br>
               For queries, contact us at info@bhandaripackersandmovers.in</p>
        </div>
    </div>
</body>
</html>
