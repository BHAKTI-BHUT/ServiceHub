<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class CommissionService
{
    /**
     * Deduct 20% commission from vendor wallet upon booking completion.
     */
    public function deductCommission(Booking $booking)
    {
        $this->processSettlement($booking, 'cash');
    }

    /**
     * Process wallet transaction based on payment method.
     */
    public function processSettlement(Booking $booking, string $paymentMethod)
    {
        if (!$booking->vendor_id) {
            return;
        }

        // We only process if transaction hasn't been logged yet for this booking
        $alreadyProcessed = WalletTransaction::where('booking_id', $booking->id)->exists();
        if ($alreadyProcessed) {
            return;
        }

        DB::transaction(function () use ($booking, $paymentMethod) {
            $amount = $booking->amount;
            $commission = $amount * 0.20;
            $settlement = $amount * 0.80;

            // 1. Update Booking with commission and settlement amounts
            $booking->update([
                'vendor_commission_amount' => $commission,
                'vendor_settlement_amount' => $settlement,
            ]);

            // 2. Fetch or create Vendor's wallet
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $booking->vendor_id],
                ['balance' => 0.00]
            );

            if ($paymentMethod === 'cash') {
                // Cash payment to supervisor/vendor: deduct 20% commission from wallet balance
                $wallet->balance -= $commission;
                $wallet->save();

                // Create wallet transaction log (debit)
                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'booking_id' => $booking->id,
                    'amount' => -$commission,
                    'type' => 'debit',
                    'description' => '20% platform commission debited (Cash trip) for Booking #' . $booking->booking_number,
                ]);
            } elseif ($paymentMethod === 'admin') {
                // Online/direct payment to admin: credit 80% settlement to wallet balance
                $wallet->balance += $settlement;
                $wallet->save();

                // Create wallet transaction log (credit)
                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'booking_id' => $booking->id,
                    'amount' => $settlement,
                    'type' => 'credit',
                    'description' => '80% settlement credited (Admin payment) for Booking #' . $booking->booking_number,
                ]);
            }
        });
    }
}
