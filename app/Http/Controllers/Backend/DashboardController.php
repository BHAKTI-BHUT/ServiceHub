<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index($page = 'dashboard')
    {
        $user = auth()->user();
        if ($user && $user->hasRole('Vendor')) {
            return redirect()->route('vendor.dashboard');
        }
        if ($user && $user->hasRole('Superviser')) {
            return redirect()->route('supervisor.dashboard');
        }

        $allowedPages = ['dashboard'];

        if (in_array($page, $allowedPages) && view()->exists($page)) {
            // Fetch Admin Dynamic Stats
            $stats = [
                'total_customers'   => \App\Models\User::role('Customer')->count(),
                'active_customers'  => \App\Models\User::role('Customer')->where('status', 'active')->count(),
                'total_bookings'    => \App\Models\Booking::count(),
                'completed_bookings'=> \App\Models\Booking::where('status', 'completed')->count(),
                'pending_bookings'  => \App\Models\Booking::where('status', 'pending')->count(),
                'confirmed_bookings'=> \App\Models\Booking::where('status', 'confirmed')->count(),
                'cancelled_bookings'=> \App\Models\Booking::where('status', 'cancelled')->count(),
                'total_revenue'     => \App\Models\Booking::where('remaining_payment_status', 'paid')->sum('amount') + \App\Models\Booking::where('registration_payment_status', 'paid')->sum('registration_charge'),
                'pending_revenue'   => \App\Models\Booking::where('remaining_payment_status', 'pending')->where('status', '!=', 'cancelled')->sum('remaining_amount'),
                'total_vehicles'    => \App\Models\Vehicle::count(),
                'active_vehicles'   => \App\Models\Vehicle::where('status', 1)->count(),
            ];

            return view($page, compact('stats'));
        }

        abort(404);
    }

    public function vendorIndex()
    {
        $user = auth()->user();
        $stats = [
            'total_bookings'     => \App\Models\Booking::where('vendor_id', $user->id)->count(),
            'pending_requests'   => \App\Models\Booking::where('vendor_id', $user->id)->where('vendor_acceptance_status', 'pending')->count(),
            'accepted_bookings'  => \App\Models\Booking::where('vendor_id', $user->id)->where('vendor_acceptance_status', 'accepted')->count(),
            'completed_bookings' => \App\Models\Booking::where('vendor_id', $user->id)->where('status', 'completed')->count(),
            'pending_bookings'   => \App\Models\Booking::where('vendor_id', $user->id)->where('status', 'pending')->count(),
            'confirmed_bookings' => \App\Models\Booking::where('vendor_id', $user->id)->where('status', 'confirmed')->count(),
            'cancelled_bookings' => \App\Models\Booking::where('vendor_id', $user->id)->where('status', 'cancelled')->count(),
            'total_revenue'      => \App\Models\Booking::where('vendor_id', $user->id)->where('status', 'completed')->sum('vendor_settlement_amount'),
            'pending_revenue'    => \App\Models\Booking::where('vendor_id', $user->id)->where('status', '!=', 'cancelled')->where('remaining_payment_status', 'pending')->sum('remaining_amount'),
        ];

        return view('Backend.Vendor.dashboard', compact('stats'));
    }

    public function supervisorIndex()
    {
        $user = auth()->user();
        $stats = [
            'total_bookings'     => \App\Models\Booking::where('supervisor_id', $user->id)->count(),
            'pending_requests'   => \App\Models\Booking::where('supervisor_id', $user->id)->where('supervisor_acceptance_status', 'pending')->count(),
            'accepted_bookings'  => \App\Models\Booking::where('supervisor_id', $user->id)->where('supervisor_acceptance_status', 'accepted')->count(),
            'completed_bookings' => \App\Models\Booking::where('supervisor_id', $user->id)->where('status', 'completed')->count(),
            'active_bookings'    => \App\Models\Booking::where('supervisor_id', $user->id)->where('status', 'in_progress')->count(),
        ];

        return view('Backend.Supervisor.dashboard', compact('stats'));
    }
}
