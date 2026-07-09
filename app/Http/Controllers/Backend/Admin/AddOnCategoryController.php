<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AddOnCategory;
use Yajra\DataTables\Facades\DataTables;

class AddOnCategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = AddOnCategory::query();
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('status_badge', fn($row) => $row->status == 'active'
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>')
                ->addColumn('action', function($row) {
                    $editBtn = '<a href="' . route('admin.addon-categories.edit', $row->id) . '" class="btn icon-btn-sm btn-light-primary" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Edit" data-drawer="true" data-drawer-title="Edit Category"><i class="ri-pencil-line"></i></a>';
                    $deleteForm = '<form action="' . route('admin.addon-categories.destroy', $row->id) . '" method="POST" class="delete-form" style="display:inline;">' . csrf_field() . method_field("DELETE") . '<button type="submit" class="btn icon-btn-sm btn-light-danger delete-item" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Delete"><i class="ri-delete-bin-line"></i></button></form>';
                    return '<div class="hstack gap-2 fs-15">' . $editBtn . $deleteForm . '</div>';
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }
        return view('Backend.Admin.AddOnCategory.Index');
    }

    public function create()
    {
        return view('Backend.Admin.AddOnCategory.Form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'   => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);
        $category = AddOnCategory::create($data);
        return response()->json(['success' => true, 'message' => 'Category created successfully.', 'data' => $category]);
    }

    public function show($id)
    {
        $category = AddOnCategory::findOrFail($id);
        return response()->json($category);
    }

    public function edit($id)
    {
        $category = AddOnCategory::findOrFail($id);
        return view('Backend.Admin.AddOnCategory.Form', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = AddOnCategory::findOrFail($id);
        $data = $request->validate([
            'name'   => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);
        $category->update($data);
        return response()->json(['success' => true, 'message' => 'Category updated successfully.', 'data' => $category]);
    }

    public function destroy($id)
    {
        AddOnCategory::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Category deleted successfully.']);
    }
}
