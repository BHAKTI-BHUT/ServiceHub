<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ItemSize;
use Yajra\DataTables\Facades\DataTables;

class ItemSizeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ItemSize::query()->orderBy('id', 'asc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    if ($row->status == 'active') {
                        return '<span class="badge bg-success-subtle text-success border border-success-subtle">Active</span>';
                    } else {
                        return '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">Inactive</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    $editBtn = '<button type="button" class="btn btn-sm btn-info text-white me-1 edit-btn" data-url="' . route('admin.item-sizes.edit', $row->id) . '" title="Edit"><i class="ri-edit-2-line"></i></button>';
                    $deleteBtn = '<button type="button" class="btn btn-sm btn-danger delete-btn" data-url="' . route('admin.item-sizes.destroy', $row->id) . '" title="Delete"><i class="ri-delete-bin-line"></i></button>';
                    return $editBtn . $deleteBtn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('Backend.Admin.ItemSize.Index');
    }

    public function create()
    {
        return view('Backend.Admin.ItemSize.Form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'size_name' => 'required|string|max:255',
            'volume_score' => 'required|numeric|min:0.1',
            'status' => 'required|in:active,inactive',
        ]);

        ItemSize::create($request->all());

        return response()->json(['success' => true, 'message' => 'Item Size created successfully.']);
    }

    public function edit(ItemSize $itemSize)
    {
        return view('Backend.Admin.ItemSize.Form', compact('itemSize'));
    }

    public function update(Request $request, ItemSize $itemSize)
    {
        $request->validate([
            'size_name' => 'required|string|max:255',
            'volume_score' => 'required|numeric|min:0.1',
            'status' => 'required|in:active,inactive',
        ]);

        $itemSize->update($request->all());

        return response()->json(['success' => true, 'message' => 'Item Size updated successfully.']);
    }

    public function destroy(ItemSize $itemSize)
    {
        // Don't delete if items are attached
        if ($itemSize->items()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Cannot delete because items are assigned to this size.']);
        }

        $itemSize->delete();
        return response()->json(['success' => true, 'message' => 'Item Size deleted successfully.']);
    }
}
