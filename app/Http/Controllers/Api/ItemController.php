<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Return all active items with size information.
     */
    public function index(Request $request)
    {
        $items = Item::with('size')
            ->where('status', 'active')
            ->orderBy('item_name')
            ->get();

        return response()->json([
            'success' => true,
            'items'   => $items,
        ]);
    }
}
