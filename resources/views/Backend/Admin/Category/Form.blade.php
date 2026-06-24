<div id="drawer-form-content">
    <form action="{{ isset($category) ? route('admin.categories.update', $category->id) : route('admin.categories.store') }}" method="POST">
        @csrf
        @if(isset($category))
            @method('PUT')
        @endif
        <div class="mb-3">
            <label class="form-label">Category Name</label>
            <input type="text" class="form-control" name="category_name" value="{{ old('category_name', $category->category_name ?? '') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Vehicle</label>
            <select class="form-select" name="vehicle_id" required>
                <option value="">Select Vehicle</option>
                @foreach($vehicles as $vehicle)
                    <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $category->vehicle_id ?? '') == $vehicle->id ? 'selected' : '' }}>
                        {{ $vehicle->vehicle_name }} (Cap: {{ $vehicle->vehicle_capacity_score }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Min Score</label>
                <input type="number" class="form-control" name="min_score" value="{{ old('min_score', $category->min_score ?? '') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Max Score</label>
                <input type="number" class="form-control" name="max_score" value="{{ old('max_score', $category->max_score ?? '') }}" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Base Fare (₹)</label>
            <input type="number" class="form-control" name="base_fare" value="{{ old('base_fare', $category->base_fare ?? '') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" name="status">
                <option value="1" {{ old('status', $category->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('status', $category->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
    </form>
</div>
