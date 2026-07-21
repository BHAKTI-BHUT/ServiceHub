<div id="drawer-form-content">
    <form action="{{ isset($itemSize) ? route('admin.item-sizes.update', $itemSize->id) : route('admin.item-sizes.store') }}" method="POST">
        @csrf
        @if(isset($itemSize))
            @method('PUT')
        @endif
        <div class="mb-3">
            <label class="form-label">Size Name (e.g. Small, Medium)</label>
            <input type="text" class="form-control" name="size_name" value="{{ old('size_name', $itemSize->size_name ?? '') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" name="status">
                <option value="active" {{ old('status', $itemSize->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $itemSize->status ?? 'active') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
    </form>
</div>
