<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Feedback::with(['booking', 'customer']);
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('booking_no', fn($row) => $row->booking ? $row->booking->booking_number : 'N/A')
                ->addColumn('customer_name', fn($row) => $row->customer ? $row->customer->name : 'N/A')
                ->addColumn('rating_stars', function($row) {
                    $stars = '';
                    for($i=1; $i<=5; $i++) {
                        if($i <= $row->rating) $stars .= '<i class="ri-star-fill text-warning"></i>';
                        else $stars .= '<i class="ri-star-line text-muted"></i>';
                    }
                    return $stars;
                })
                ->addColumn('label', function($row) {
                    if($row->rating >= 4) return '<span class="badge bg-success">Excellent</span>';
                    if($row->rating == 3) return '<span class="badge bg-warning">Average</span>';
                    return '<span class="badge bg-danger">Poor</span>';
                })
                ->editColumn('created_at', fn($row) => $row->created_at->format('d M, Y'))
                ->rawColumns(['rating_stars', 'label'])
                ->make(true);
        }
        return view('Backend.Admin.Feedback.Index');
    }
}
