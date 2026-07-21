<div id="drawer-form-content">
    <form id="editCustomerForm" action="{{ route('customer.update', $customer->id) }}" method="POST"
        class="needs-validation" novalidate enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-12 text-center mb-2">
                <div class="position-relative d-inline-block">
                    <img id="custImagePreview" 
                        src="{{ $customer->image ? asset($customer->image) : asset('assets/images/avatar/dummy-avatar.jpg') }}" 
                        alt="Avatar" 
                        class="rounded-circle border" 
                        width="100" 
                        height="100"
                        style="object-fit: cover;">
                    <label for="imageUpload" class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle p-1" style="width:30px; height:30px; display:flex; align-items:center; justify-content:center;">
                        <i class="ri-camera-line"></i>
                        <input type="file" id="imageUpload" name="image" class="d-none" accept="image/*">
                    </label>
                </div>
            </div>
            
            <div class="col-md-12">
                <label for="cust_name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="cust_name" name="name"
                    placeholder="Full Name" value="{{ $customer->name }}" required>
                <div class="invalid-feedback">Please enter a name.</div>
            </div>
            
            <div class="col-md-12">
                <label for="cust_email" class="form-label">Email</label>
                <input type="email" class="form-control" id="cust_email" name="email"
                    placeholder="Email" value="{{ $customer->email }}" required>
                <div class="invalid-feedback">Please provide a valid email.</div>
            </div>
            
            <div class="col-md-6">
                <label for="cust_mobile" class="form-label">Mobile Number</label>
                <input type="text" class="form-control" id="cust_mobile" name="mobile"
                    placeholder="Mobile Number" value="{{ $customer->mobile }}" required>
                <div class="invalid-feedback">Please enter a mobile number.</div>
            </div>
            
            <div class="col-md-6">
                <label for="cust_status" class="form-label">Status</label>
                <select class="form-select" id="cust_status" name="status" required>
                    <option value="active" {{ $customer->status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $customer->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <div class="invalid-feedback">Please select a status.</div>
            </div>

            <div class="col-md-12">
                <label for="cust_city" class="form-label">City</label>
                <input type="text" class="form-control" id="cust_city" name="city"
                    placeholder="City" value="{{ $customer->city }}">
            </div>

            <div class="col-md-12">
                <label for="cust_address" class="form-label">Address</label>
                <textarea class="form-control" id="cust_address" name="address" rows="2" placeholder="Address">{{ $customer->address }}</textarea>
            </div>
            
            <div class="col-md-12">
                <label for="cust_password" class="form-label">Password (Leave blank to keep current)</label>
                <input type="password" class="form-control" id="cust_password" name="password"
                    placeholder="New Password" minlength="6">
                <div class="invalid-feedback">Password must be at least 6 characters.</div>
            </div>
        </div>
    </form>

    <script>
        // Image preview logic
        document.getElementById('imageUpload').addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('custImagePreview').src = e.target.result;
                }
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    </script>
</div>
