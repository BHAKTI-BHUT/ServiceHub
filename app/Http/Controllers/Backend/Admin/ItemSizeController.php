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
                    $editBtn = '<a href="' . route('admin.item-sizes.edit', $row->id) . '" class="btn icon-btn-sm btn-light-primary" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Edit" data-drawer="true" data-drawer-title="Edit Item Size"><i class="ri-pencil-line"></i></a>';
                    $deleteForm = '<form action="' . route('admin.item-sizes.destroy', $row->id) . '" method="POST" class="delete-form" style="display:inline;">' . csrf_field() . method_field("DELETE") . '<button type="submit" class="btn icon-btn-sm btn-light-danger delete-item" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Delete"><i class="ri-delete-bin-line"></i></button></form>';
                    return '<div class="hstack gap-2 fs-15">' . $editBtn . $deleteForm . '</div>';
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
        $data = $request->validate([
            'size_name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        ItemSize::create($data);

        return response()->json(['success' => true, 'message' => 'Item Size created successfully.']);
    }

    public function edit(ItemSize $itemSize)
    {
        return view('Backend.Admin.ItemSize.Form', compact('itemSize'));
    }

    public function update(Request $request, ItemSize $itemSize)
    {
        $data = $request->validate([
            'size_name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $itemSize->update($data);

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
