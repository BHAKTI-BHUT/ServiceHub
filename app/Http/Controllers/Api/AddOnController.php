<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AddOn;
use Illuminate\Http\Request;

class AddOnController extends Controller
{
    /**
     * Return all active add‑ons.
     */
    public function index(Request $request)
    {
        $addons = AddOn::where('status', 'active')
            ->orderBy('addon_name')
            ->get();

        return response()->json([
            'success' => true,
            'addons'  => $addons,
        ]);
    }
}
