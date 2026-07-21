<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

use App\Models\ItemSize;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Item::with('size')->select('items.*');
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('size_name', function ($row) {
                    return $row->size ? $row->size->size_name : '-';
                })
                ->addColumn('score_point', function ($row) {
                    return '<span class="badge bg-primary-subtle text-primary fw-bold px-2 py-1">' . number_format($row->score_point ?? 0, 2) . ' pts</span>';
                })
                ->addColumn('status_badge', fn($row) => $row->status == 'active'
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>')
                ->addColumn('action', function($row) {
                    $editBtn = '<a href="' . route('admin.items.edit', $row->id) . '" class="btn icon-btn-sm btn-light-primary" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Edit" data-drawer="true" data-drawer-title="Edit Item"><i class="ri-pencil-line"></i></a>';
                    $deleteForm = '<form action="' . route('admin.items.destroy', $row->id) . '" method="POST" class="delete-form" style="display:inline;">' . csrf_field() . method_field("DELETE") . '<button type="submit" class="btn icon-btn-sm btn-light-danger delete-item" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Delete"><i class="ri-delete-bin-line"></i></button></form>';
                    return '<div class="hstack gap-2 fs-15">' . $editBtn . $deleteForm . '</div>';
                })
                ->rawColumns(['score_point', 'status_badge', 'action'])
                ->make(true);
        }
        return view('Backend.Admin.Item.Index');
    }

    public function create()
    {
        $sizes = ItemSize::where('status', 'active')->get();
        return view('Backend.Admin.Item.Form', compact('sizes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'item_name'    => 'required|string|max:255',
            'item_size_id' => 'required|exists:item_sizes,id',
            'score_point'  => 'required|numeric|min:0',
            'status'       => 'required|boolean',
        ]);
        $item = Item::create($data);
        return response()->json(['success' => true, 'message' => 'Item created successfully.', 'data' => $item]);
    }

    public function show($id)
    {
        $item = Item::with('size')->findOrFail($id);
        return response()->json($item);
    }

    public function edit($id)
    {
        $item = Item::findOrFail($id);
        $sizes = ItemSize::where('status', 'active')->get();
        return view('Backend.Admin.Item.Form', compact('item', 'sizes'));
    }

    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        $data = $request->validate([
            'item_name'    => 'required|string|max:255',
            'item_size_id' => 'required|exists:item_sizes,id',
            'score_point'  => 'required|numeric|min:0',
            'status'       => 'required|boolean',
        ]);
        $item->update($data);
        return response()->json(['success' => true, 'message' => 'Item updated successfully.', 'data' => $item]);
    }

    public function destroy($id)
    {
        Item::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Item deleted successfully.']);
    }
}
