<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    public function index()
    {
        return view('Backend.Admin.Pricing.Index');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.pricing')->with('success', 'Pricing settings saved successfully.');
    }
}
