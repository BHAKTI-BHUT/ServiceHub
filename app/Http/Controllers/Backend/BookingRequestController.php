<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingRequest;
use Illuminate\Http\Request;

class BookingRequestController extends Controller
{
    /**
     * Display a listing of the booking requests.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $requests = BookingRequest::with('customer')->orderBy('created_at', 'desc');

            return datatables()->of($requests)
                ->addColumn('customer_name', function ($req) {
                    return $req->customer ? $req->customer->name : '<span class="text-muted">—</span>';
                })
                ->addColumn('customer_mobile', function ($req) {
                    return $req->customer ? ($req->customer->mobile ?? '—') : '—';
                })
                ->editColumn('shifting_date', function ($req) {
                    $time = $req->shifting_time ? date('h:i A', strtotime($req->shifting_time)) : '—';
                    $date = $req->shifting_date ? date('d M Y', strtotime($req->shifting_date)) : '—';
                    return '<div>' . $date . '</div><span class="text-muted fs-11">' . $time . '</span>';
                })
                ->editColumn('pickup_location', function ($req) {
                    return '<div class="text-wrap" style="min-width: 200px; max-width: 320px; white-space: normal;">' . e($req->pickup_location) . '</div>';
                })
                ->editColumn('drop_location', function ($req) {
                    return '<div class="text-wrap" style="min-width: 200px; max-width: 320px; white-space: normal;">' . e($req->drop_location) . '</div>';
                })
                ->addColumn('status', function ($req) {
                    if ($req->status === 'pending') {
                        $badge = 'bg-warning-focus text-warning';
                    } elseif ($req->status === 'approved') {
                        $badge = 'bg-success-focus text-success';
                    } else {
                        $badge = 'bg-danger-focus text-danger';
                    }
                    return '<span class="badge ' . $badge . '">' . ucfirst($req->status) . '</span>';
                })
                ->addColumn('action', function ($req) {
                    $viewBtn = '<a href="' . route('booking-request.show', $req->id) . '" class="btn icon-btn-sm btn-light-info" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="View Request" data-drawer="true" data-drawer-title="Request Details"><i class="ri-eye-line"></i></a>';
                    return '<div class="hstack gap-2 fs-15">' . $viewBtn . '</div>';
                })
                ->rawColumns(['customer_name', 'shifting_date', 'status', 'action', 'pickup_location', 'drop_location'])
                ->make(true);
        }

        return view('Backend.BookingRequest.Index');
    }

    /**
     * Display the specified booking request details.
     */
    public function show(BookingRequest $bookingRequest)
    {
        $bookingRequest->load('customer');
        return view('Backend.BookingRequest.Show', compact('bookingRequest'));
    }

    /**
     * Approve the booking request and convert it into a booking.
     */
    public function approve(Request $request, BookingRequest $bookingRequest)
    {
        if ($bookingRequest->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This request has already been ' . $bookingRequest->status . '.'
            ], 400);
        }

        // Update booking request status
        $bookingRequest->update(['status' => 'approved']);

        // Create booking record
        $booking = Booking::create([
            'customer_id' => $bookingRequest->customer_id,
            'booking_request_id' => $bookingRequest->id,
            'pickup_location' => $bookingRequest->pickup_location,
            'drop_location' => $bookingRequest->drop_location,
            'pickup_latitude' => $bookingRequest->pickup_latitude,
            'pickup_longitude' => $bookingRequest->pickup_longitude,
            'drop_latitude' => $bookingRequest->drop_latitude,
            'drop_longitude' => $bookingRequest->drop_longitude,
            'shifting_date' => $bookingRequest->shifting_date,
            'shifting_time' => $bookingRequest->shifting_time,
            'amount' => $bookingRequest->estimated_amount ?? 0.00,
            'status' => 'confirmed', // approved requests are confirmed bookings
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Request approved and Booking #' . $booking->booking_number . ' created successfully!',
            'booking' => $booking
        ]);
    }

    /**
     * Reject the booking request.
     */
    public function reject(Request $request, BookingRequest $bookingRequest)
    {
        if ($bookingRequest->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This request has already been ' . $bookingRequest->status . '.'
            ], 400);
        }

        $bookingRequest->update(['status' => 'rejected']);

        return response()->json([
            'success' => true,
            'message' => 'Booking request rejected successfully!'
        ]);
    }
}
