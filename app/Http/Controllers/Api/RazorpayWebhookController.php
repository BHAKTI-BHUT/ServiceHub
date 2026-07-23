<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\OrderTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RazorpayWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature');
        
        $webhookSecret = config('services.razorpay.webhook_secret');
        // Fallback to key_secret if webhook_secret is not specifically set in env
        if (!$webhookSecret) {
            $webhookSecret = config('services.razorpay.key_secret');
        }

        if (!$webhookSecret) {
            Log::error('Razorpay Webhook Secret not configured.');
            return response()->json(['error' => 'Webhook secret not configured'], 500);
        }

        if (!$signature) {
            return response()->json(['error' => 'Missing signature'], 400);
        }

        $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);

        if (!hash_equals($expectedSignature, $signature)) {
            Log::error('Razorpay Webhook Signature Mismatch.');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $event = json_decode($payload, true);

        Log::info('Razorpay Webhook Received: ' . ($event['event'] ?? 'unknown'));

        if (isset($event['event']) && in_array($event['event'], ['order.paid', 'payment.captured'])) {
            $paymentEntity = $event['payload']['payment']['entity'] ?? null;
            
            if ($paymentEntity && isset($paymentEntity['order_id'])) {
                $orderId = $paymentEntity['order_id'];
                $paymentId = $paymentEntity['id'];

                $booking = Booking::where('registration_order_id', $orderId)->first();

                if ($booking) {
                    if ($booking->registration_payment_status !== 'paid') {
                        $booking->update([
                            'status'                      => 'confirmed',
                            'tracking_status'             => 'confirmed',
                            'registration_payment_status' => 'paid',
                            'registration_payment_id'     => $paymentId,
                            'remaining_amount'            => $booking->amount - $booking->registration_charge,
                        ]);

                        OrderTracking::create([
                            'booking_id' => $booking->id,
                            'status' => 'confirmed',
                            'notes' => 'Registration payment of Rs. ' . $booking->registration_charge . ' verified via Webhook. Booking confirmed.',
                        ]);

                        Log::info("Booking {$booking->id} updated via Razorpay Webhook for Order: {$orderId}");
                    } else {
                        Log::info("Booking {$booking->id} was already marked as paid. Ignoring Webhook for Order: {$orderId}");
                    }
                } else {
                    Log::warning("Razorpay Webhook: No booking found for Order ID: {$orderId}");
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
