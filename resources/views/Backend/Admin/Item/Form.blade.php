<div id="drawer-form-content">
    <form action="{{ isset($item) ? route('admin.items.update', $item->id) : route('admin.items.store') }}" method="POST">
        @csrf
        @if(isset($item))
            @method('PUT')
        @endif
        <div class="mb-3">
            <label class="form-label">Item Name</label>
            <input type="text" class="form-control" name="item_name" value="{{ old('item_name', $item->item_name ?? '') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Item Size</label>
            <select class="form-select" name="item_size_id" required>
                <option value="">Select Size...</option>
                @foreach($sizes as $size)
                    <option value="{{ $size->id }}" {{ old('item_size_id', $item->item_size_id ?? '') == $size->id ? 'selected' : '' }}>
                        {{ $size->size_name }} ({{ $size->volume_score }} pts)
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" name="status">
                <option value="1" {{ old('status', $item->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('status', $item->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
    </form>
</div>
