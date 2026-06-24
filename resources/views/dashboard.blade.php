@extends('partials.Layouts.master3')

@section('title', 'Dashboard | Herozi - Design & Developed by Bhakti.')
@section('sub-title', 'Dashboard Details')
@section('pagetitle', 'Dashboard')
@section('buttonTitle', 'Share')
@section('link', '#!')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/air-datepicker/air-datepicker.css') }}">
    <link href="{{ asset('assets/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
@endsection

@section('content')

    <div class="row">
        <!-- Customers Card -->
        <div class="col-lg-3 col-md-6">
            <div class="card card-hover overflow-hidden">
                <div class="card-body hstack gap-2">
                    <div class="avatar avatar-item rounded-2 bg-primary bg-opacity-10 text-primary">
                        <i class="ri-user-star-line fs-20"></i>
                    </div>
                    <div>
                        <span class="mb-2 fs-12 text-muted">Total Customers</span>
                        <h5 class="fw-medium mb-1">{{ number_format($stats['total_customers']) }}</h5>
                    </div>
                </div>
                <div class="card-body bg-light py-2 bg-opacity-40 hstack justify-content-between gap-3">
                    <div class="hstack gap-2">
                        <h6 class="mb-0 fw-semibold fs-12">Active:</h6>
                        <p class="fs-12 text-muted mb-0">{{ number_format($stats['active_customers']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bookings Card -->
        <div class="col-lg-3 col-md-6">
            <div class="card card-hover overflow-hidden">
                <div class="card-body hstack gap-2">
                    <div class="avatar avatar-item rounded-2 bg-success bg-opacity-10 text-success">
                        <i class="ri-calendar-todo-line fs-20"></i>
                    </div>
                    <div>
                        <span class="mb-2 fs-12 text-muted">Total Bookings</span>
                        <h5 class="fw-medium mb-1">{{ number_format($stats['total_bookings']) }}</h5>
                    </div>
                </div>
                <div class="card-body bg-light py-2 bg-opacity-40 hstack justify-content-between gap-3">
                    <div class="hstack gap-2">
                        <h6 class="mb-0 fw-semibold fs-12">Completed:</h6>
                        <p class="fs-12 text-success mb-0">{{ number_format($stats['completed_bookings']) }}</p>
                    </div>
                    <div class="vr h-30px align-self-center bg-light"></div>
                    <div class="hstack gap-2">
                        <h6 class="mb-0 fw-semibold fs-12">Pending:</h6>
                        <p class="fs-12 text-warning mb-0">{{ number_format($stats['pending_bookings']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Card -->
        <div class="col-lg-3 col-md-6">
            <div class="card card-hover overflow-hidden">
                <div class="card-body hstack gap-2">
                    <div class="avatar avatar-item rounded-2 bg-info bg-opacity-10 text-info">
                        <i class="ri-money-dollar-circle-line fs-20"></i>
                    </div>
                    <div>
                        <span class="mb-2 fs-12 text-muted">Total Revenue</span>
                        <h5 class="fw-medium mb-1">₹{{ number_format($stats['total_revenue'], 2) }}</h5>
                    </div>
                </div>
                <div class="card-body bg-light py-2 bg-opacity-40 hstack justify-content-between gap-3">
                    <div class="hstack gap-2">
                        <h6 class="mb-0 fw-semibold fs-12">Pending:</h6>
                        <p class="fs-12 text-muted mb-0">₹{{ number_format($stats['pending_revenue'], 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vehicles Card -->
        <div class="col-lg-3 col-md-6">
            <div class="card card-hover overflow-hidden">
                <div class="card-body hstack gap-2">
                    <div class="avatar avatar-item rounded-2 bg-warning bg-opacity-10 text-warning">
                        <i class="ri-truck-line fs-20"></i>
                    </div>
                    <div>
                        <span class="mb-2 fs-12 text-muted">Total Vehicles</span>
                        <h5 class="fw-medium mb-1">{{ number_format($stats['total_vehicles']) }}</h5>
                    </div>
                </div>
                <div class="card-body bg-light py-2 bg-opacity-40 hstack justify-content-between gap-3">
                    <div class="hstack gap-2">
                        <h6 class="mb-0 fw-semibold fs-12">Active:</h6>
                        <p class="fs-12 text-muted mb-0">{{ number_format($stats['active_vehicles']) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/pages/countup.init.js') }}"></script>
    <script src="{{ asset('assets/libs/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/air-datepicker/air-datepicker.js') }}"></script>
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/js/charts/apexcharts-config.init.js') }}"></script>
    <script src="{{ asset('assets/js/dashboards/dashboard-online-course.init.js') }}"></script>
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
