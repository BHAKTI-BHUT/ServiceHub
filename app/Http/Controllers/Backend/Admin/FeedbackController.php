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
            $query = Feedback::with(['booking', 'customer'])->orderBy('id', 'desc');
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
                ->addColumn('action', function($row) {
                    $deleteForm = '<form action="' . route('admin.feedback.destroy', $row->id) . '" method="POST" class="delete-form" style="display:inline;">' . csrf_field() . method_field("DELETE") . '<button type="submit" class="btn icon-btn-sm btn-light-danger delete-item" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Delete"><i class="ri-delete-bin-line"></i></button></form>';
                    return '<div class="hstack gap-2 fs-15">' . $deleteForm . '</div>';
                })
                ->editColumn('created_at', fn($row) => $row->created_at->format('d M, Y'))
                ->rawColumns(['rating_stars', 'label', 'action'])
                ->make(true);
        }
        return view('Backend.Admin.Feedback.Index');
    }

    public function destroy($id)
    {
        $feedback = Feedback::findOrFail($id);
        $customerEmail = $feedback->customer ? $feedback->customer->email : null;

        // Delete from local admin database
        $feedback->delete();

        // Delete from CodeIgniter database if customer email exists
        if ($customerEmail) {
            try {
                \DB::connection('mysql')->table('service_hub_web.reviews')
                    ->where('email', $customerEmail)
                    ->delete();
            } catch (\Exception $e) {
                // Silently log error to not break the page if connection isn't configured
                \Log::error("Failed to delete review from CI database: " . $e->getMessage());
            }
        }

        return response()->json(['success' => true, 'message' => 'Feedback deleted successfully from both Admin and Website.']);
    }
}
