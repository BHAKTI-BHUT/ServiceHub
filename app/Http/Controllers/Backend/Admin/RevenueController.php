<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Booking::with('customer')->orderBy('created_at', 'desc');
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('customer_name', fn($row) => $row->customer ? $row->customer->name : 'N/A')
                ->addColumn('payment_status_badge', function($row) {
                    if ($row->remaining_payment_status == 'paid') {
                        return '<span class="badge bg-success">Fully Paid</span>';
                    }
                    return '<span class="badge bg-danger">Unpaid</span>';
                })
                ->editColumn('amount', fn($row) => '₹' . number_format($row->amount, 2))
                ->editColumn('registration_charge', function($row) {
                    $defaultRegFee = \App\Models\PricingSetting::where('key', 'registration_fee')->value('value') ?? 500;
                    $fee = $row->registration_charge ?? $defaultRegFee;
                    return '₹' . number_format($fee, 2) . ' (' . ucfirst($row->registration_payment_status) . ')';
                })
                ->editColumn('remaining_amount', function($row) {
                    if ($row->remaining_payment_status === 'paid') {
                        return '₹0.00';
                    }
                    $defaultRegFee = \App\Models\PricingSetting::where('key', 'registration_fee')->value('value') ?? 500;
                    $remaining = $row->amount - ($row->registration_charge ?? $defaultRegFee);
                    return '₹' . number_format($remaining, 2);
                })
                ->editColumn('created_at', fn($row) => $row->created_at->format('d M, Y'))
                ->addColumn('action', function ($row) {
                    $detailsUrl = route('admin.revenue.details', $row->id);
                    $invoiceUrl = route('admin.revenue.invoice', $row->id);
                    return '<a href="' . $detailsUrl . '" target="_blank" class="btn btn-sm btn-light-primary me-1" data-bs-toggle="tooltip" title="View Details"><i class="ri-eye-line"></i> Details</a>' .
                           '<a href="' . $invoiceUrl . '" target="_blank" class="btn btn-sm btn-light-success" data-bs-toggle="tooltip" title="View & Print Tax Invoice"><i class="ri-file-list-3-line"></i> Invoice</a>';
                })
                ->rawColumns(['payment_status_badge', 'action'])
                ->make(true);
        }

        // Summary Statistics (for cards)
        $defaultRegFee = \App\Models\PricingSetting::where('key', 'registration_fee')->value('value') ?? 500;

        $registrationCollected = Booking::where('registration_payment_status', 'paid')
            ->get()
            ->sum(fn($b) => $b->registration_charge ?? $defaultRegFee);

        $remainingCollected = Booking::where('remaining_payment_status', 'paid')
            ->get()
            ->sum(fn($b) => $b->amount - ($b->registration_charge ?? $defaultRegFee));

        $totalRevenue = $remainingCollected + $registrationCollected;

        $pendingRevenue = Booking::where('remaining_payment_status', 'pending')
            ->where('status', '!=', 'cancelled')
            ->get()
            ->sum(fn($b) => $b->amount - ($b->registration_charge ?? $defaultRegFee));

        return view('Backend.Admin.Revenue.Index', compact(
            'totalRevenue', 'registrationCollected', 'remainingCollected', 'pendingRevenue'
        ));
    }
    public function details(Booking $booking)
    {
        $booking->load(['customer', 'items', 'addOns', 'category', 'vehicle']);
        return view('Backend.Admin.Revenue.details', compact('booking'));
    }

    public function invoice(Booking $booking)
    {
        $booking->load(['customer', 'items', 'addOns', 'category', 'vehicle']);
        return view('invoices.tax_invoice', compact('booking'));
    }
}
