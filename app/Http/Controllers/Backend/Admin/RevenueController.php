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
                    } elseif ($row->advance_payment_status == 'paid') {
                        return '<span class="badge bg-warning">Advance Paid</span>';
                    }
                    return '<span class="badge bg-danger">Unpaid</span>';
                })
                ->editColumn('amount', fn($row) => '₹' . number_format($row->amount, 2))
                ->editColumn('advance_amount', fn($row) => '₹' . number_format($row->advance_amount, 2))
                ->editColumn('remaining_amount', fn($row) => '₹' . number_format($row->remaining_amount, 2))
                ->editColumn('created_at', fn($row) => $row->created_at->format('d M, Y'))
                ->rawColumns(['payment_status_badge'])
                ->make(true);
        }

        // Summary Statistics (for cards)
        $totalRevenue = Booking::where('remaining_payment_status', 'paid')->sum('amount');
        $advanceCollected = Booking::where('advance_payment_status', 'paid')->sum('advance_amount');
        $remainingCollected = Booking::where('remaining_payment_status', 'paid')->sum('remaining_amount');
        $pendingRevenue = Booking::where('remaining_payment_status', 'pending')->where('status', '!=', 'cancelled')->sum('remaining_amount');

        return view('Backend.Admin.Revenue.Index', compact(
            'totalRevenue', 'advanceCollected', 'remainingCollected', 'pendingRevenue'
        ));
    }
}
