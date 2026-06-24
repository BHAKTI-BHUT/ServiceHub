<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\AddOn;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AddOnController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = AddOn::query();
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('status_badge', fn($row) => $row->status
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>')
                ->addColumn('action', function($row) {
                    $editBtn = '<a href="' . route('admin.addons.edit', $row->id) . '" class="btn icon-btn-sm btn-light-primary" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Edit" data-drawer="true" data-drawer-title="Edit Service"><i class="ri-pencil-line"></i></a>';
                    $deleteForm = '<form action="' . route('admin.addons.destroy', $row->id) . '" method="POST" class="delete-form" style="display:inline;">' . csrf_field() . method_field("DELETE") . '<button type="submit" class="btn icon-btn-sm btn-light-danger delete-item" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Delete"><i class="ri-delete-bin-line"></i></button></form>';
                    return '<div class="hstack gap-2 fs-15">' . $editBtn . $deleteForm . '</div>';
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }
        return view('Backend.Admin.AddOn.Index');
    }

    public function create()
    {
        return view('Backend.Admin.AddOn.Form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'addon_name' => 'required|string|max:255',
            'price'      => 'required|numeric|min:0',
            'status'     => 'required|boolean',
        ]);
        $addon = AddOn::create($data);
        return response()->json(['success' => true, 'message' => 'Service created successfully.', 'data' => $addon]);
    }

    public function show($id)
    {
        $addon = AddOn::findOrFail($id);
        return response()->json($addon);
    }

    public function edit($id)
    {
        $addon = AddOn::findOrFail($id);
        return view('Backend.Admin.AddOn.Form', compact('addon'));
    }

    public function update(Request $request, $id)
    {
        $addon = AddOn::findOrFail($id);
        $data = $request->validate([
            'addon_name' => 'required|string|max:255',
            'price'      => 'required|numeric|min:0',
            'status'     => 'required|boolean',
        ]);
        $addon->update($data);
        return response()->json(['success' => true, 'message' => 'Service updated successfully.', 'data' => $addon]);
    }

    public function destroy($id)
    {
        AddOn::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Service deleted successfully.']);
    }
}
