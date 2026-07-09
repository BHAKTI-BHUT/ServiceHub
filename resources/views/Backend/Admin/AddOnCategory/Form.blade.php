<form action="{{ isset($category) ? route('admin.addon-categories.update', $category->id) : route('admin.addon-categories.store') }}" method="POST" id="drawerForm">
    @csrf
    @if(isset($category))
        @method('PUT')
    @endif
    <div class="row">
        <div class="col-md-12 mb-3">
            <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="name" name="name" value="{{ isset($category) ? $category->name : old('name') }}" placeholder="e.g. Packing and boxes" required>
        </div>
        <div class="col-md-12 mb-3">
            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
            <select class="form-select" id="status" name="status" required>
                <option value="active" {{ (isset($category) && $category->status == 'active') ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ (isset($category) && $category->status == 'inactive') ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
    </div>
</form>
