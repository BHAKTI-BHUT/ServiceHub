<div id="drawer-form-content">
    <form action="{{ isset($vehicle) ? route('admin.vehicles.update', $vehicle->id) : route('admin.vehicles.store') }}" method="POST">
        @csrf
        @if(isset($vehicle))
            @method('PUT')
        @endif
        <div class="mb-3">
            <label class="form-label">Vehicle Name</label>
            <input type="text" class="form-control" name="vehicle_name" value="{{ old('vehicle_name', $vehicle->vehicle_name ?? '') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Capacity Score</label>
            <input type="number" class="form-control" name="vehicle_capacity_score" value="{{ old('vehicle_capacity_score', $vehicle->vehicle_capacity_score ?? '') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" name="status">
                <option value="1" {{ old('status', $vehicle->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('status', $vehicle->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
    </form>
</div>
