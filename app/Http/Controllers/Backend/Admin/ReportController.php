<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Booking::with('customer')->orderBy('created_at', 'desc');

            // Apply filters
            if ($request->filled('from')) {
                $query->whereDate('shifting_date', '>=', $request->from);
            }
            if ($request->filled('to')) {
                $query->whereDate('shifting_date', '<=', $request->to);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('customer_name', fn($row) => $row->customer ? $row->customer->name : 'N/A')
                ->addColumn('status_badge', function($row) {
                    if ($row->status == 'order_confirmed') return '<span class="badge bg-success">Confirmed</span>';
                    if ($row->status == 'shifting_completed') return '<span class="badge bg-info">Completed</span>';
                    if ($row->status == 'cancelled') return '<span class="badge bg-danger">Cancelled</span>';
                    return '<span class="badge bg-warning">' . ucfirst(str_replace('_', ' ', $row->status)) . '</span>';
                })
                ->editColumn('amount', fn($row) => '₹' . number_format($row->amount, 2))
                ->rawColumns(['status_badge'])
                ->make(true);
        }

        return view('Backend.Admin.Report.Index');
    }
}
