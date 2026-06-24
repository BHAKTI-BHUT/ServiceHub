@extends('partials.layouts.master')
@section('title', 'Pricing Settings | ServiceHub')
@section('sub-title', 'Pricing')
@section('pagetitle', 'Pricing Settings')
@section('content')
<div class="row g-4">
    <div class="col-lg-8 mx-auto">
        <div class="card mb-0">
            <div class="card-header">
                <h5 class="mb-0"><i class="ri-price-tag-3-line me-2"></i>Pricing Configuration</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.pricing.store') }}" method="POST">
                    @csrf

                    <h6 class="text-muted fw-semibold mb-3 mt-1">📅 Weekend Surcharge</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Enable Weekend Surcharge</label>
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox" name="weekend_enabled" id="weekendEnabled" value="1">
                                <label class="form-check-label" for="weekendEnabled">Enable</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Weekend Surcharge (%)</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="weekend_percent" placeholder="e.g. 10" min="0" max="100">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h6 class="text-muted fw-semibold mb-3">📆 Month-End Surcharge</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Enable Month-End Surcharge</label>
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox" name="month_end_enabled" id="monthEndEnabled" value="1">
                                <label class="form-check-label" for="monthEndEnabled">Enable</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Month-End Surcharge (%)</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="month_end_percent" placeholder="e.g. 15" min="0" max="100">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h6 class="text-muted fw-semibold mb-3">🚗 Distance Pricing</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Per KM Charge (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" class="form-control" name="per_km_charge" placeholder="e.g. 20">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Advance Payment Amount (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" class="form-control" name="advance_amount" placeholder="e.g. 500">
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="ri-save-line me-1"></i> Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
    $(document).ready(function() {
        @if(session('success')) showToast('{{ session('success') }}'); @endif
    });
</script>
@endsection
