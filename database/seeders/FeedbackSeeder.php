<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Feedback;
use App\Models\Booking;

class FeedbackSeeder extends Seeder
{
    public function run(): void
    {
        $bookings = Booking::with('customer')->get();

        $reviews = [
            'Excellent service! The packing was very professional and quick.',
            'Bhanderi Packers did an amazing job with my shifting. All items arrived safely.',
            'On-time delivery and well-mannered staff. Fully satisfied with the experience.',
            'Best packing and moving service in town. No scratches on my furniture.',
            'Very smooth transition from Ahmedabad to Mumbai. Thank you Bhanderi Packers.',
            'Good experience. The team was very cooperative, polite, and hardworking.',
            'Hassle-free household shifting service at highly reasonable rates.',
            'Awesome packaging quality. Highly recommended for domestic house shifting.',
            'Excellent experience. Got all my electronic items and bed delivered safely.',
            'Extremely professional movers. They took care of everything from start to finish.',
            'Great coordination and support. Shifting was made very stress-free.',
            'They used premium quality bubble wrap for my double door fridge and TV. Safe delivery!',
            'Affordable and fast shifting. The staff worked very quickly and efficiently.',
            'Amazing cargo handling. Thank you Bhanderi packers & partner team.',
            'Outstanding service. The team was very punctual, polite, and professional.',
            'Good customer support. They answered all my queries during vehicle transit.',
            'Clean, safe, and professional shifting. Recommended to everyone.',
            'Exceptional care taken for delicate glassware. Great job by the crew!',
            'Smooth shifting from Vadodara to Pune. Very supportive local staff.',
            'Quick loading and unloading. Complete peace of mind. Excellent work!',
        ];

        // Seed 20 feedback records corresponding to the 20 bookings
        for ($i = 0; $i < min(20, count($bookings)); $i++) {
            $booking = $bookings[$i];
            
            Feedback::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'customer_id' => $booking->customer_id,
                    'rating'      => rand(4, 5), // Bhanderi is highly rated!
                    'review'      => $reviews[$i % count($reviews)],
                ]
            );
        }
    }
}
