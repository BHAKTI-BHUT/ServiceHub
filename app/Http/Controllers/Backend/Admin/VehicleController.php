<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Vehicle::query();
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('status_badge', fn($row) => $row->status
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>')
                ->addColumn('action', function($row) {
                    $editBtn = '<a href="' . route('admin.vehicles.edit', $row->id) . '" class="btn icon-btn-sm btn-light-primary" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Edit" data-drawer="true" data-drawer-title="Edit Vehicle"><i class="ri-pencil-line"></i></a>';
                    $deleteForm = '<form action="' . route('admin.vehicles.destroy', $row->id) . '" method="POST" class="delete-form" style="display:inline;">' . csrf_field() . method_field("DELETE") . '<button type="submit" class="btn icon-btn-sm btn-light-danger delete-item" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Delete"><i class="ri-delete-bin-line"></i></button></form>';
                    return '<div class="hstack gap-2 fs-15">' . $editBtn . $deleteForm . '</div>';
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }
        return view('Backend.Admin.Vehicle.Index');
    }

    public function create()
    {
        return view('Backend.Admin.Vehicle.Form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_name'           => 'required|string|max:255',
            'vehicle_capacity_score' => 'required|integer|min:1',
            'status'                 => 'required|boolean',
        ]);
        $vehicle = Vehicle::create($data);
        return response()->json(['success' => true, 'message' => 'Vehicle created successfully.', 'data' => $vehicle]);
    }

    public function show($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        return response()->json($vehicle);
    }

    public function edit($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        return view('Backend.Admin.Vehicle.Form', compact('vehicle'));
    }

    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $data = $request->validate([
            'vehicle_name'           => 'required|string|max:255',
            'vehicle_capacity_score' => 'required|integer|min:1',
            'status'                 => 'required|boolean',
        ]);
        $vehicle->update($data);
        return response()->json(['success' => true, 'message' => 'Vehicle updated successfully.', 'data' => $vehicle]);
    }

    public function destroy($id)
    {
        Vehicle::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Vehicle deleted successfully.']);
    }
}
