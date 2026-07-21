<div id="drawer-form-content">
    <form action="{{ isset($addon) ? route('admin.addons.update', $addon->id) : route('admin.addons.store') }}" method="POST">
        @csrf
        @if(isset($addon))
            @method('PUT')
        @endif
        <div class="mb-3">
            <label class="form-label">Category</label>
            <select class="form-select" name="addon_category_id">
                <option value="">-- Select Category (Optional) --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('addon_category_id', $addon->addon_category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Service Name</label>
            <input type="text" class="form-control" name="addon_name" value="{{ old('addon_name', $addon->addon_name ?? '') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Price (₹)</label>
            <input type="number" class="form-control" name="price" value="{{ old('price', $addon->price ?? '') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" name="status">
                <option value="1" {{ old('status', $addon->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('status', $addon->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
    </form>
</div>
