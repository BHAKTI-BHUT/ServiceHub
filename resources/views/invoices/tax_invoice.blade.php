<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tax Invoice - {{ $booking->booking_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; color: #000; background: #fff; line-height: 1.4; }
        .page-container { max-width: 900px; margin: 20px auto; padding: 10px; }
        .invoice-box { border: 2px solid #000; padding: 0; position: relative; }
        
        /* Company Header */
        .company-header { text-align: center; border-bottom: 2px solid #000; padding: 12px 10px; }
        .company-header h1 { font-size: 26px; font-weight: 800; letter-spacing: 1px; color: #000; margin-bottom: 2px; text-transform: uppercase; }
        .company-header p { font-size: 11px; color: #000; font-weight: 500; white-space: pre-line; }

        /* Title Box */
        .title-box { text-align: center; font-weight: 800; font-size: 14px; border-bottom: 1px solid #000; background: #f0f0f0; padding: 6px; text-transform: uppercase; letter-spacing: 1px; }

        /* Info Section (split 2 columns) */
        .info-section { display: flex; border-bottom: 1px solid #000; }
        .info-left { width: 60%; border-right: 1px solid #000; padding: 10px; }
        .info-right { width: 40%; padding: 0; }
        
        .info-right-row { display: flex; border-bottom: 1px solid #000; min-height: 28px; align-items: center; }
        .info-right-row:last-child { border-bottom: none; }
        .info-right-label { width: 45%; font-weight: 700; padding: 6px 8px; border-right: 1px solid #000; height: 100%; display: flex; align-items: center; }
        .info-right-value { width: 55%; padding: 6px 8px; height: 100%; display: flex; align-items: center; }

        .info-left p { margin-bottom: 4px; line-height: 1.5; }
        .info-left strong { font-weight: 700; }

        /* Invoice Table */
        .invoice-table { width: 100%; border-collapse: collapse; border-bottom: 1px solid #000; }
        .invoice-table th { background: #f0f0f0; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 8px 6px; font-weight: 800; font-size: 12px; text-align: center; text-transform: uppercase; }
        .invoice-table th:last-child { border-right: none; }
        .invoice-table td { border-right: 1px solid #000; padding: 8px 6px; font-size: 12px; vertical-align: top; }
        .invoice-table td:last-child { border-right: none; }
        
        .invoice-table tbody tr { min-height: 150px; }
        .invoice-table .text-center { text-align: center; }
        .invoice-table .text-right { text-align: right; }
        
        /* Spacing rows to match typical empty space in paper invoices */
        .dummy-row { height: 180px; }
        .dummy-row td { border-bottom: none; }

        /* Calculation & Footer blocks */
        .footer-blocks { display: flex; border-bottom: 1px solid #000; }
        .footer-left { width: 60%; border-right: 1px solid #000; padding: 10px; display: flex; flex-direction: column; justify-content: space-between; }
        .footer-right { width: 40%; }
        
        .footer-right-row { display: flex; border-bottom: 1px solid #000; font-size: 12px; }
        .footer-right-row:last-child { border-bottom: none; }
        .footer-right-row.highlight { font-weight: 700; background: #fafafa; }
        .footer-right-label { width: 50%; padding: 6px 8px; border-right: 1px solid #000; }
        .footer-right-value { width: 50%; padding: 6px 8px; text-align: right; font-weight: 600; }

        .bank-details { font-size: 11px; margin-bottom: 10px; line-height: 1.4; }
        .bank-details strong { font-weight: 700; }
        .gst-bill-summary { font-size: 11px; border-top: 1px dashed #ccc; padding-top: 6px; }

        /* Note & Terms Row */
        .note-terms-row { display: flex; border-bottom: 1px solid #000; }
        .note-block { width: 60%; border-right: 1px solid #000; padding: 10px; }
        .sign-block { width: 40%; padding: 10px; text-align: center; display: flex; flex-direction: column; justify-content: space-between; min-height: 110px; }
        .sign-title { font-weight: 700; font-size: 11px; text-transform: uppercase; }
        .sign-space { height: 40px; }
        .sign-bottom { font-size: 11px; color: #555; }

        .terms-block { padding: 10px; font-size: 10px; line-height: 1.4; }
        .terms-block strong { display: block; font-size: 11px; font-weight: 700; margin-bottom: 4px; text-decoration: underline; }
        .terms-block ol { padding-left: 14px; }

        /* Print styling */
        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .invoice-box { border: 2px solid #000; }
        }

        .print-btn-bar { display: flex; justify-content: flex-end; margin-bottom: 12px; }
        .btn-print { background: #000; color: #fff; border: 1px solid #000; padding: 8px 20px; font-weight: 700; font-size: 12px; cursor: pointer; text-transform: uppercase; display: flex; align-items: center; gap: 6px; }
        .btn-print:hover { background: #333; }
    </style>
</head>
<body>
    <div class="page-container">
        <!-- Print Button Bar -->
        <div class="print-btn-bar no-print">
            <button onclick="window.print()" class="btn-print">
                🖨️ Print / Download PDF
            </button>
        </div>

        <div class="invoice-box">
            <!-- Company Header -->
            <div class="company-header">
                <h1>Bhandari Packers & Movers</h1>
                <p>Exclusive Packing, Moving & Logistics Solutions | 1- New Laxmi Soc. 150 Ft. Ring Road, Nr. Balaji Hall, Mavdi Plot, Rajkot.
                GSTIN: 24GIZPS9434M1ZY | Ph: +91 95129 32626 | Email: info@bhandaripackersandmovers.in</p>
            </div>

            <!-- Tax Invoice Label -->
            <div class="title-box">
                Tax Invoice
            </div>

            <!-- Info Section -->
            <div class="info-section">
                <!-- Customer Details -->
                <div class="info-left">
                    <p><strong>M/s.:</strong> {{ $booking->customer->name ?? 'N/A' }}</p>
                    <p><strong>Address:</strong> {{ $booking->pickup_location }}</p>
                    <p><strong>To Drop:</strong> {{ $booking->drop_location }}</p>
                    <p><strong>MO:</strong> {{ $booking->customer->mobile ?? '—' }}</p>
                    <p><strong>Place of Supply:</strong> {{ $booking->customer->city ?? 'Gujarat' }}</p>
                    <p><strong>GSTIN No.:</strong> {{ $booking->customer->gstin ?? 'N/A' }}</p>
                </div>
                
                <!-- Invoice Info -->
                <div class="info-right">
                    <div class="info-right-row">
                        <div class="info-right-label">Invoice No.:</div>
                        <div class="info-right-value" style="font-family: monospace; font-weight: bold;">INV-{{ $booking->booking_number }}</div>
                    </div>
                    <div class="info-right-row">
                        <div class="info-right-label">Date:</div>
                        <div class="info-right-value">{{ date('d/m/Y', strtotime($booking->shifting_date)) }}</div>
                    </div>
                    <div class="info-right-row">
                        <div class="info-right-label">Payment Mode:</div>
                        <div class="info-right-value">{{ $booking->registration_payment_id ? 'Online' : 'Cash' }}</div>
                    </div>
                    <div class="info-right-row">
                        <div class="info-right-label">PO/Ref.:</div>
                        <div class="info-right-value">customer from rajkot</div>
                    </div>
                </div>
            </div>

            <!-- Item Table -->
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">Sr.</th>
                        <th style="width: 45%;">Product Name</th>
                        <th style="width: 15%;">HSN/SAC</th>
                        <th style="width: 8%;">Qty</th>
                        <th style="width: 12%;">Rate (₹)</th>
                        <th style="width: 5%;">GST</th>
                        <th style="width: 10%;">Amount (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // GST calculations
                        // Assuming overall price includes 18% GST (9% CGST + 9% SGST)
                        $total_amount = $booking->amount;
                        $taxable_subtotal = round($total_amount / 1.18, 2);
                        $total_gst = round($total_amount - $taxable_subtotal, 2);
                        $cgst = round($total_gst / 2, 2);
                        $sgst = $total_gst - $cgst;
                        
                        $defaultRegFee = \App\Models\PricingSetting::where('key', 'registration_fee')->value('value') ?? 500;
                        $sr = 1;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $sr++ }}</td>
                        <td>
                            <strong>Professional Shifting Service</strong>
                            <div style="font-size: 10px; color: #555; margin-top: 2px;">
                                Base Fare + Shifting Distance Charge
                            </div>
                        </td>
                        <td class="text-center">996511</td>
                        <td class="text-center">1.000</td>
                        <td class="text-right">{{ number_format($taxable_subtotal, 2) }}</td>
                        <td class="text-center">18.00%</td>
                        <td class="text-right">{{ number_format($taxable_subtotal, 2) }}</td>
                    </tr>
                    
                    {{-- Spacer to create traditional layout --}}
                    <tr class="dummy-row">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

            <!-- Calculations Blocks -->
            <div class="footer-blocks">
                <!-- Bottom Left Banking and Totals Info -->
                <div class="footer-left">
                    <div class="bank-details">
                        <strong>GSTIN No.:</strong> 24GIZPS9434M1ZY<br>
                        <strong>Bank Name:</strong> IDFC FIRST BANK<br>
                        <strong>A/c No.:</strong> 10108735383<br>
                        <strong>RTGS/IFSC:</strong> IDFB0042425
                    </div>
                    <div class="gst-bill-summary">
                        <strong>Total GST:</strong> ₹{{ number_format($total_gst, 2) }}<br>
                        <strong>Bill Amount:</strong> ₹{{ number_format($total_amount, 2) }}
                    </div>
                </div>

                <!-- Bottom Right Financial Summary -->
                <div class="footer-right">
                    <div class="footer-right-row">
                        <div class="footer-right-label">Sub Total</div>
                        <div class="footer-right-value">₹{{ number_format($taxable_subtotal, 2) }}</div>
                    </div>
                    <div class="footer-right-row">
                        <div class="footer-right-label">Taxable Amount</div>
                        <div class="footer-right-value">₹{{ number_format($taxable_subtotal, 2) }}</div>
                    </div>
                    <div class="footer-right-row">
                        <div class="footer-right-label">Central Tax (9%)</div>
                        <div class="footer-right-value">₹{{ number_format($cgst, 2) }}</div>
                    </div>
                    <div class="footer-right-row">
                        <div class="footer-right-label">State/UT Tax (9%)</div>
                        <div class="footer-right-value">₹{{ number_format($sgst, 2) }}</div>
                    </div>
                    <div class="footer-right-row highlight">
                        <div class="footer-right-label">Grand Total</div>
                        <div class="footer-right-value">₹{{ number_format($total_amount, 2) }}</div>
                    </div>
                    <div class="footer-right-row">
                        <div class="footer-right-label">Advance Paid</div>
                        <div class="footer-right-value">
                            ₹{{ number_format($booking->registration_payment_status === 'paid' ? ($booking->registration_charge ?? $defaultRegFee) : 0, 2) }}
                        </div>
                    </div>
                    <div class="footer-right-row highlight" style="color: red;">
                        <div class="footer-right-label" style="color: red;">Due Balance</div>
                        <div class="footer-right-value" style="color: red;">
                            ₹{{ $booking->remaining_payment_status === 'paid' ? '0.00' : number_format($total_amount - ($booking->registration_payment_status === 'paid' ? ($booking->registration_charge ?? $defaultRegFee) : 0), 2) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes & Signature Row -->
            <div class="note-terms-row">
                <div class="note-block">
                    <strong>Note:</strong> Shifting from {{ $booking->pickup_location }} to {{ $booking->drop_location }}
                </div>
                <div class="sign-block">
                    <div class="sign-title">For, Bhandari Packers & Movers</div>
                    <div class="sign-space"></div>
                    <div class="sign-bottom">(Authorised Signatory)</div>
                </div>
            </div>

            <!-- Terms and Conditions -->
            <div class="terms-block">
                <strong>Terms & Conditions:</strong>
                <ol>
                    <li>Goods once sold or service rendered will not be taken back/refunded.</li>
                    <li>Interest @18% p.a. will be charged if payment is not made within due date.</li>
                    <li>Our risk and responsibility ceases as soon as the goods leave our premises or vehicle starts transit.</li>
                    <li>Subject to 'RAJKOT' Jurisdiction only. E. & O.E.</li>
                </ol>
            </div>
        </div>
    </div>
</body>
</html>
