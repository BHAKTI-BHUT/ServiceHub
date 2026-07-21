<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\RefundRequest;
use App\Models\Booking;
use App\Models\OrderTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class RefundRequestController extends Controller
{
    /**
     * Display a listing of refund requests.    
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = RefundRequest::with(['booking', 'customer'])->orderBy('id', 'desc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('booking_no', fn($row) => $row->booking ? $row->booking->booking_number : 'N/A')
                ->addColumn('customer_name', fn($row) => $row->customer ? $row->customer->name : 'N/A')
                ->addColumn('customer_mobile', fn($row) => $row->customer ? $row->customer->mobile : 'N/A')
                ->editColumn('payment_type', function($row) {
                    $class = match($row->payment_type) {
                        'advance' => 'bg-purple-focus text-purple',
                        'final' => 'bg-teal-focus text-teal',
                        default => 'bg-orange-focus text-orange'
                    };
                    return '<span class="badge ' . $class . '">' . strtoupper($row->payment_type) . '</span>';
                })
                ->editColumn('total_paid_amount', fn($row) => '₹' . number_format($row->total_paid_amount, 2))
                ->editColumn('requested_refund_amount', fn($row) => '₹' . number_format($row->requested_refund_amount, 2))
                ->editColumn('approved_refund_amount', fn($row) => $row->approved_refund_amount > 0 ? '₹' . number_format($row->approved_refund_amount, 2) : '—')
                ->editColumn('status', function($row) {
                    $badge = match($row->status) {
                        'pending' => 'bg-warning-focus text-warning',
                        'approved' => 'bg-info-focus text-info',
                        'processing' => 'bg-secondary-focus text-secondary',
                        'refunded' => 'bg-success-focus text-success',
                        'rejected' => 'bg-danger-focus text-danger',
                        default => 'bg-light text-dark'
                    };
                    return '<span class="badge ' . $badge . '">' . ucfirst($row->status) . '</span>';
                })
                ->editColumn('created_at', fn($row) => $row->created_at->format('d M, Y h:i A'))
                ->addColumn('action', function($row) {
                    $viewBtn = '<button type="button" class="btn icon-btn-sm btn-light-info view-refund-btn" data-id="' . $row->id . '" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="View Details"><i class="ri-eye-line"></i></button>';
                    
                    $actions = '<div class="hstack gap-2 justify-content-end">' . $viewBtn;

                    if ($row->status === 'pending') {
                        $actions .= '
                            <button type="button" class="btn icon-btn-sm btn-light-success approve-refund-btn" data-id="' . $row->id . '" data-amount="' . $row->requested_refund_amount . '" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Approve Request">
                                <i class="ri-check-line"></i>
                            </button>
                            <button type="button" class="btn icon-btn-sm btn-light-danger reject-refund-btn" data-id="' . $row->id . '" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Reject Request">
                                <i class="ri-close-line"></i>
                            </button>';
                    } elseif ($row->status === 'approved') {
                        $actions .= '
                            <button type="button" class="btn icon-btn-sm btn-light-warning process-refund-btn" data-id="' . $row->id . '" data-amount="' . $row->approved_refund_amount . '" data-method="' . $row->refund_method . '" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Process Refund">
                                <i class="ri-hand-coin-line"></i>
                            </button>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['payment_type', 'status', 'action'])
                ->make(true);
        }

        // Stats summary
        $stats = [
            'total' => RefundRequest::count(),
            'pending' => RefundRequest::where('status', 'pending')->count(),
            'approved' => RefundRequest::where('status', 'approved')->count(),
            'refunded' => RefundRequest::where('status', 'refunded')->count(),
            'rejected' => RefundRequest::where('status', 'rejected')->count(),
            'total_refunded' => RefundRequest::where('status', 'refunded')->sum('approved_refund_amount'),
        ];

        return view('Backend.Admin.Refunds.Index', compact('stats'));
    }

    /**
     * Show details of a specific refund request.
     */
    public function show(RefundRequest $refund)
    {
        $refund->load(['booking', 'customer']);
        return response()->json([
            'success' => true,
            'data' => $refund,
            'booking_no' => $refund->booking?->booking_number ?? 'N/A',
            'customer_name' => $refund->customer?->name ?? 'N/A',
            'customer_mobile' => $refund->customer?->mobile ?? 'N/A',
            'formatted_created_at' => $refund->created_at->format('d M, Y h:i A'),
            'formatted_updated_at' => $refund->updated_at->format('d M, Y h:i A'),
        ]);
    }

    /**
     * Approve a refund request.
     */
    public function approve(Request $request, RefundRequest $refund)
    {
        $request->validate([
            'approved_amount' => 'required|numeric|min:0',
            'admin_remarks' => 'nullable|string'
        ]);

        if ($refund->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'This request is already processed.']);
        }

        try {
            DB::transaction(function () use ($refund, $request) {
                $approvedAmount = $request->approved_amount > 0 ? $request->approved_amount : $refund->requested_refund_amount;

                $refund->update([
                    'status' => 'approved',
                    'approved_refund_amount' => $approvedAmount,
                    'admin_remarks' => $request->admin_remarks ?? 'Refund request approved by admin.',
                ]);

                // Update booking status to cancelled
                if ($refund->booking) {
                    $refund->booking->update([
                        'status' => 'cancelled',
                        'tracking_status' => 'cancelled'
                    ]);

                    // Add tracking record
                    OrderTracking::create([
                        'booking_id' => $refund->booking_id,
                        'status' => 'cancelled',
                        'notes' => 'Booking cancelled and refund approved for Rs. ' . number_format($approvedAmount, 2) . '. Remarks: ' . $request->admin_remarks
                    ]);
                }
            });

            return response()->json(['success' => true, 'message' => 'Refund request approved successfully.']);
        } catch (\Exception $e) {
            Log::error("Refund Approve Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to approve request: ' . $e->getMessage()]);
        }
    }

    /**
     * Reject a refund request.
     */
    public function reject(Request $request, RefundRequest $refund)
    {
        $request->validate([
            'admin_remarks' => 'required|string'
        ]);

        if ($refund->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'This request is already processed.']);
        }

        try {
            DB::transaction(function () use ($refund, $request) {
                $refund->update([
                    'status' => 'rejected',
                    'admin_remarks' => $request->admin_remarks,
                ]);

                // Restore booking to confirmed status
                if ($refund->booking) {
                    $refund->booking->update([
                        'status' => 'confirmed',
                        'tracking_status' => 'confirmed'
                    ]);

                    // Add tracking record
                    OrderTracking::create([
                        'booking_id' => $refund->booking_id,
                        'status' => 'confirmed',
                        'notes' => 'Refund request rejected by admin. Reason: ' . $request->admin_remarks
                    ]);
                }
            });

            return response()->json(['success' => true, 'message' => 'Refund request rejected.']);
        } catch (\Exception $e) {
            Log::error("Refund Reject Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to reject request: ' . $e->getMessage()]);
        }
    }

    /**
     * Process/Execute the refund (make actual Razorpay transfer or record manual bank transfer).
     */
    public function process(Request $request, RefundRequest $refund)
    {
        if ($refund->status !== 'approved') {
            return response()->json(['success' => false, 'message' => 'Only approved requests can be processed.']);
        }

        $gatewayRefundId = null;

        // If refund mode is original_source, attempt Razorpay refund api integration
        if ($refund->refund_method === 'original_source') {
            $paymentId = $refund->gateway_payment_id;
            
            if (!$paymentId) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Payment ID is missing for this booking. Please process manually.'
                ]);
            }

            $keyId = env('RAZORPAY_KEY_ID', 'rzp_live_TFidvL3276AhNp');
            $keySecret = env('RAZORPAY_KEY_SECRET', 'mlbDSjCya1UISGeqITg0b5r0');
            
            // Amount in paise
            $amountInPaise = round($refund->approved_refund_amount * 100);

            try {
                $response = Http::withBasicAuth($keyId, $keySecret)
                    ->post("https://api.razorpay.com/v1/payments/{$paymentId}/refund", [
                        'amount' => $amountInPaise,
                        'notes' => [
                            'refund_request_id' => $refund->id,
                            'booking_id' => $refund->booking_id,
                        ]
                    ]);

                if ($response->successful()) {
                    $resData = $response->json();
                    $gatewayRefundId = $resData['id'] ?? null;
                } else {
                    $err = $response->json();
                    $errMsg = $err['error']['description'] ?? 'Razorpay refund API call failed.';
                    Log::error("Razorpay Refund API Fail: " . json_encode($err));
                    return response()->json(['success' => false, 'message' => 'Razorpay Error: ' . $errMsg]);
                }
            } catch (\Exception $e) {
                Log::error("Razorpay API Exception: " . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Payment gateway connection error: ' . $e->getMessage()]);
            }
        } else {
            // Manual UPI / Bank Transfer
            $request->validate([
                'gateway_refund_id' => 'required|string', // Reference transaction ID
            ]);
            $gatewayRefundId = $request->gateway_refund_id;
        }

        try {
            DB::transaction(function () use ($refund, $gatewayRefundId, $request) {
                $refund->update([
                    'status' => 'refunded',
                    'gateway_refund_id' => $gatewayRefundId,
                    'admin_remarks' => $request->admin_remarks ?? 'Refund successfully processed.',
                ]);

                if ($refund->booking) {
                    // Update tracking status
                    OrderTracking::create([
                        'booking_id' => $refund->booking_id,
                        'status' => 'cancelled',
                        'notes' => 'Refund of Rs. ' . number_format($refund->approved_refund_amount, 2) . ' processed successfully. Refund Ref ID: ' . $gatewayRefundId
                    ]);
                }
            });

            return response()->json(['success' => true, 'message' => 'Refund processed and marked as Refunded.']);
        } catch (\Exception $e) {
            Log::error("Refund Process DB Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Database update failed: ' . $e->getMessage()]);
        }
    }
}
