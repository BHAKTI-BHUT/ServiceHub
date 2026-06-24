@php
    $isCustomerActive      = request()->routeIs('customer.*');
    $isBookingActive       = request()->routeIs('booking.*');
    $isBookingReqActive    = request()->routeIs('booking-request.*');
    $isBookingsActive      = $isBookingActive || $isBookingReqActive;
    $isUserActive          = request()->routeIs('user.*');
    $isRoleActive          = request()->routeIs('role.*');
    $isUserMgmtActive      = $isUserActive || $isRoleActive;
    $isSettingsActive      = request()->routeIs('settings.*') || request()->routeIs('profile.*');
    $isVehicleActive       = request()->routeIs('admin.vehicles');
    $isCategoryActive      = request()->routeIs('admin.categories');
    $isItemActive          = request()->routeIs('admin.items');
    $isAddonActive         = request()->routeIs('admin.addons');
    $isPricingActive       = request()->routeIs('admin.pricing');
    $isRevenueActive       = request()->routeIs('admin.revenue');
    $isFeedbackActive      = request()->routeIs('admin.feedback');
    $isReportActive        = request()->routeIs('admin.reports');
    $isMastersActive       = $isVehicleActive || $isCategoryActive || $isItemActive || $isAddonActive;
    $isFinanceActive       = $isRevenueActive || $isFeedbackActive || $isReportActive;
@endphp

<ul class="main-menu" id="all-menu-items" role="menu">

    {{-- ── MAIN ────────────────────────────────────────────── --}}
    <li class="menu-title" role="presentation">Main</li>
    <li class="slide">
        <a href="{{ route('dashboard') }}"
           class="side-menu__item {{ request()->routeIs('dashboard') ? 'active' : '' }}" role="menuitem">
            <span class="side_menu_icon"><i class="ri-home-2-line"></i></span>
            <span class="side-menu__label">Dashboard</span>
        </a>
    </li>

    {{-- ── SERVICE MANAGEMENT ──────────────────────────────── --}}
    <li class="menu-title" role="presentation">Service Management</li>

    {{-- Customers --}}
    <li class="slide">
        <a href="{{ route('customer.index') }}"
           class="side-menu__item {{ $isCustomerActive ? 'active' : '' }}" role="menuitem">
            <span class="side_menu_icon"><i class="ri-user-star-line"></i></span>
            <span class="side-menu__label">Customers</span>
        </a>
    </li>

    {{-- Bookings (dropdown) --}}
    <li class="slide {{ $isBookingsActive ? 'active open' : '' }}">
        <a href="#!" class="side-menu__item {{ $isBookingsActive ? 'active' : '' }}" role="menuitem">
            <span class="side_menu_icon"><i class="ri-calendar-todo-line"></i></span>
            <span class="side-menu__label">Bookings</span>
            <i class="ri-arrow-down-s-line side-menu__angle"></i>
        </a>
        <ul class="slide-menu" role="menu">
            <li class="slide">
                <a href="{{ route('booking-request.index') }}"
                   class="side-menu__item {{ $isBookingReqActive ? 'active' : '' }}" role="menuitem">
                    Booking Requests
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('booking.index') }}"
                   class="side-menu__item {{ $isBookingActive ? 'active' : '' }}" role="menuitem">
                    Booking Manage
                </a>
            </li>
        </ul>
    </li>

    {{-- Revenue --}}
    <li class="slide">
        <a href="{{ route('admin.revenue') }}"
           class="side-menu__item {{ $isRevenueActive ? 'active' : '' }}" role="menuitem">
            <span class="side_menu_icon"><i class="ri-money-dollar-circle-line"></i></span>
            <span class="side-menu__label">Revenue</span>
        </a>
    </li>

    {{-- ── MASTER MANAGEMENT ───────────────────────────────── --}}
    <li class="menu-title" role="presentation">Master Management</li>

    <li class="slide {{ $isMastersActive ? 'active open' : '' }}">
        <a href="#!" class="side-menu__item {{ $isMastersActive ? 'active' : '' }}" role="menuitem">
            <span class="side_menu_icon"><i class="ri-database-2-line"></i></span>
            <span class="side-menu__label">Master Settings</span>
            <i class="ri-arrow-down-s-line side-menu__angle"></i>
        </a>
        <ul class="slide-menu" role="menu">
            {{-- Vehicles --}}
            <li class="slide">
                <a href="{{ route('admin.vehicles') }}"
                   class="side-menu__item {{ $isVehicleActive ? 'active' : '' }}" role="menuitem">
                    Vehicles
                </a>
            </li>

            {{-- Categories --}}
            <li class="slide">
                <a href="{{ route('admin.categories') }}"
                   class="side-menu__item {{ $isCategoryActive ? 'active' : '' }}" role="menuitem">
                    Categories
                </a>
            </li>

            {{-- Item Sizes Master --}}
            <li class="slide">
                <a href="{{ route('admin.item-sizes') }}"
                   class="side-menu__item {{ request()->routeIs('admin.item-sizes*') ? 'active' : '' }}" role="menuitem">
                    Item Sizes Master
                </a>
            </li>

            {{-- Item Master --}}
            <li class="slide">
                <a href="{{ route('admin.items') }}"
                   class="side-menu__item {{ $isItemActive ? 'active' : '' }}" role="menuitem">
                    Item Master
                </a>
            </li>

            {{-- Add-On Services --}}
            <li class="slide">
                <a href="{{ route('admin.addons') }}"
                   class="side-menu__item {{ $isAddonActive ? 'active' : '' }}" role="menuitem">
                    Add-On Services
                </a>
            </li>
        </ul>
    </li>

    {{-- ── REPORTS & FEEDBACK ──────────────────────────────── --}}
    <li class="menu-title" role="presentation">Reports & Feedback</li>

    <li class="slide">
        <a href="{{ route('admin.feedback') }}"
           class="side-menu__item {{ $isFeedbackActive ? 'active' : '' }}" role="menuitem">
            <span class="side_menu_icon"><i class="ri-star-line"></i></span>
            <span class="side-menu__label">Feedback & Ratings</span>
        </a>
    </li>

    <li class="slide">
        <a href="{{ route('admin.reports') }}"
           class="side-menu__item {{ $isReportActive ? 'active' : '' }}" role="menuitem">
            <span class="side_menu_icon"><i class="ri-bar-chart-line"></i></span>
            <span class="side-menu__label">Reports</span>
        </a>
    </li>

    {{-- ── APPLICATIONS ────────────────────────────────────── --}}
    <li class="menu-title" role="presentation">Applications</li>

    {{-- User Management (dropdown) --}}
    <li class="slide {{ $isUserMgmtActive ? 'active open' : '' }}">
        <a href="#!" class="side-menu__item {{ $isUserMgmtActive ? 'active' : '' }}" role="menuitem">
            <span class="side_menu_icon"><i class="ri-group-line"></i></span>
            <span class="side-menu__label">User Management</span>
            <i class="ri-arrow-down-s-line side-menu__angle"></i>
        </a>
        <ul class="slide-menu" role="menu">
            <li class="slide">
                <a href="{{ route('user.index') }}"
                   class="side-menu__item {{ $isUserActive ? 'active' : '' }}" role="menuitem">
                    Users
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('role.index') }}"
                   class="side-menu__item {{ $isRoleActive ? 'active' : '' }}" role="menuitem">
                    Roles & Permissions
                </a>
            </li>
        </ul>
    </li>

    {{-- ── SETTINGS ─────────────────────────────────────────── --}}
    <li class="menu-title" role="presentation">Settings</li>

    <li class="slide {{ $isSettingsActive ? 'active open' : '' }}">
        <a href="#!" class="side-menu__item {{ $isSettingsActive ? 'active' : '' }}" role="menuitem">
            <span class="side_menu_icon"><i class="ri-settings-3-line"></i></span>
            <span class="side-menu__label">System Settings</span>
            <i class="ri-arrow-down-s-line side-menu__angle"></i>
        </a>
        <ul class="slide-menu" role="menu">
            <li class="slide">
                <a href="{{ route('settings.edit') }}"
                   class="side-menu__item {{ request()->routeIs('settings.edit') ? 'active' : '' }}" role="menuitem">
                    General Settings
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('admin.pricing') }}"
                   class="side-menu__item {{ $isPricingActive ? 'active' : '' }}" role="menuitem">
                    Pricing Settings
                </a>
            </li>
            <li class="slide">
                <a href="{{ route('profile.edit') }}"
                   class="side-menu__item {{ request()->routeIs('profile.edit') ? 'active' : '' }}" role="menuitem">
                    Profile Settings
                </a>
            </li>
        </ul>
    </li>

</ul>
