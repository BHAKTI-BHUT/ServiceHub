<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundRequest extends Model
{
    use HasFactory;

    protected $table = 'refund_requests';

    protected $fillable = [
        'booking_id',
        'customer_id',
        'payment_type',
        'total_paid_amount',
        'requested_refund_amount',
        'approved_refund_amount',
        'cancellation_reason',
        'reason_details',
        'refund_method',
        'upi_id',
        'bank_account_no',
        'bank_ifsc',
        'status',
        'gateway_payment_id',
        'gateway_refund_id',
        'admin_remarks',
    ];

    /**
     * Get the booking associated with the refund request.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    /**
     * Get the customer associated with the refund request.
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
