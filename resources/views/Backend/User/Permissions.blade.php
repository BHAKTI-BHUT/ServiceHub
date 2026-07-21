@extends('partials.layouts.master')

@section('title', $user->name . "'s Permissions | ServiceHub")
@section('sub-title', $user->name . "'s Permissions")
@section('pagetitle', 'User Permissions')

@section('css')
<style>
    .permission-accordion .accordion-item {
        border: 1px solid var(--bs-border-color);
        border-radius: 8px !important;
        margin-bottom: 10px;
        overflow: hidden;
    }
    .permission-accordion .accordion-button {
        background: var(--bs-card-bg);
        font-weight: 600;
        font-size: 15px;
        padding: 14px 20px;
        box-shadow: none;
    }
    .permission-accordion .accordion-button:not(.collapsed) {
        background: rgba(var(--bs-primary-rgb), 0.05);
        color: var(--bs-heading-color);
    }
    .permission-accordion .accordion-body {
        padding: 15px 20px;
        background: var(--bs-card-bg);
        border-top: 1px solid var(--bs-border-color);
    }
    .permission-badge {
        font-size: 11px;
        padding: 3px 8px;
        border-radius: 4px;
    }
    .module-checkbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
        flex-shrink: 0;
    }
    .perm-check-item {
        padding: 8px 12px;
        border-radius: 6px;
        transition: background 0.2s;
    }
    .perm-check-item:hover {
        background: var(--bs-light);
    }
    .perm-check-item label {
        font-size: 14px;
    }
    .user-info-card {
        background: linear-gradient(135deg, rgba(var(--bs-primary-rgb), 0.08), rgba(var(--bs-primary-rgb), 0.02));
        border: 1px solid rgba(var(--bs-primary-rgb), 0.2);
        border-radius: 10px;
        padding: 16px 20px;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('user.index') }}">Users</a></li>
                <li class="breadcrumb-item active" aria-current="page">Permissions</li>
            </ol>
        </nav>
    </div>

    <div class="col-12">
        <div class="card mb-0">
            <div class="card-body">

                {{-- User Info --}}
                <div class="user-info-card d-flex align-items-center gap-3 mb-4">
                    <div class="avatar-sm rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold fs-5" style="width:45px;height:45px;min-width:45px;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="fw-semibold fs-6">{{ $user->name }}</div>
                        <div class="text-muted small">{{ $user->email }}</div>
                    </div>
                    <div class="ms-auto">
                        @foreach($user->getRoleNames() as $role)
                            <span class="badge bg-primary">{{ $role }}</span>
                        @endforeach
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex gap-2 flex-wrap mb-4">
                    <button type="button" class="btn btn-sm btn-outline-info" id="expandAllBtn">
                        <i class="ri-arrow-down-s-line me-1"></i>Expand All
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="collapseAllBtn">
                        <i class="ri-arrow-up-s-line me-1"></i>Collapse All
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-success" id="selectAllBtn">
                        <i class="ri-checkbox-multiple-line me-1"></i>Select All
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="deselectAllBtn">
                        <i class="ri-close-circle-line me-1"></i>Deselect All
                    </button>
                </div>

                {{-- Permissions Form --}}
                <form id="permissionsForm" action="{{ route('user.permissions.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="accordion permission-accordion" id="permissionsAccordion">
                        @foreach ($modules as $moduleName => $permissions)
                            @php
                                $moduleSlug = Str::slug($moduleName);
                                $permCount  = count($permissions);
                                $checkedCount = count(array_intersect($permissions, $userPermissions));
                                $allChecked   = ($permCount > 0) && ($checkedCount === $permCount);
                            @endphp
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading_{{ $moduleSlug }}">
                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapse_{{ $moduleSlug }}"
                                        aria-expanded="false"
                                        aria-controls="collapse_{{ $moduleSlug }}">
                                        <div class="d-flex align-items-center gap-3 w-100 me-3">
                                            <input type="checkbox"
                                                class="form-check-input module-checkbox select-all-module m-0"
                                                data-module="{{ $moduleSlug }}"
                                                id="module_{{ $moduleSlug }}"
                                                {{ $allChecked ? 'checked' : '' }}
                                                onclick="event.stopPropagation();">
                                            <span>{{ $moduleName }}</span>
                                            <span class="badge bg-primary-subtle text-primary permission-badge ms-auto">
                                                {{ $checkedCount }}/{{ $permCount }}
                                            </span>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse_{{ $moduleSlug }}"
                                    class="accordion-collapse collapse"
                                    aria-labelledby="heading_{{ $moduleSlug }}">
                                    <div class="accordion-body">
                                        <div class="row g-1">
                                            @foreach ($permissions as $permission)
                                                <div class="col-md-6 col-lg-4">
                                                    <div class="perm-check-item d-flex align-items-center gap-2">
                                                        <input type="checkbox"
                                                            class="form-check-input permission-checkbox m-0 perm-item"
                                                            data-module="{{ $moduleSlug }}"
                                                            name="permissions[]"
                                                            value="{{ $permission }}"
                                                            id="perm_{{ Str::slug($permission) }}"
                                                            {{ in_array($permission, $userPermissions) ? 'checked' : '' }}>
                                                        <label class="form-check-label mb-0 w-100"
                                                            for="perm_{{ Str::slug($permission) }}"
                                                            style="cursor:pointer;">
                                                            {{ ucwords($permission) }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Save Button --}}
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4" id="savePermissionsBtn">
                            <i class="ri-save-line me-1"></i>Save Permissions
                        </button>
                        <a href="{{ route('user.index') }}" class="btn btn-light px-4">
                            <i class="ri-arrow-left-line me-1"></i>Back to Users
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function () {

    // ── Expand All ─────────────────────────────────────────────
    $('#expandAllBtn').on('click', function () {
        $('.permission-accordion .accordion-collapse').each(function () {
            var bsCollapse = bootstrap.Collapse.getOrCreateInstance(this, { toggle: false });
            bsCollapse.show();
        });
    });

    // ── Collapse All ────────────────────────────────────────────
    $('#collapseAllBtn').on('click', function () {
        $('.permission-accordion .accordion-collapse').each(function () {
            var bsCollapse = bootstrap.Collapse.getOrCreateInstance(this, { toggle: false });
            bsCollapse.hide();
        });
    });

    // ── Select All ──────────────────────────────────────────────
    $('#selectAllBtn').on('click', function () {
        $('.permission-checkbox').prop('checked', true);
        $('.select-all-module').prop('checked', true);
        updateAllBadges();
    });

    // ── Deselect All ────────────────────────────────────────────
    $('#deselectAllBtn').on('click', function () {
        $('.permission-checkbox').prop('checked', false);
        $('.select-all-module').prop('checked', false);
        updateAllBadges();
    });

    // ── Module checkbox: check/uncheck all in that module ───────
    $(document).on('change', '.select-all-module', function () {
        var moduleSlug = $(this).data('module');
        var isChecked  = $(this).is(':checked');
        $('.perm-item[data-module="' + moduleSlug + '"]').prop('checked', isChecked);
        updateModuleBadge(moduleSlug);
    });

    // ── Individual permission checkbox: update module header ─────
    $(document).on('change', '.permission-checkbox', function () {
        var moduleSlug = $(this).data('module');
        if (!moduleSlug) return;

        var total   = $('.perm-item[data-module="' + moduleSlug + '"]').length;
        var checked = $('.perm-item[data-module="' + moduleSlug + '"]:checked').length;

        $('#module_' + moduleSlug).prop('checked', total > 0 && total === checked);
        updateModuleBadge(moduleSlug);
    });

    // ── Helper: update single module badge ──────────────────────
    function updateModuleBadge(moduleSlug) {
        var total   = $('.perm-item[data-module="' + moduleSlug + '"]').length;
        var checked = $('.perm-item[data-module="' + moduleSlug + '"]:checked').length;
        $('#heading_' + moduleSlug + ' .permission-badge').text(checked + '/' + total);
    }

    // ── Helper: update all module badges ────────────────────────
    function updateAllBadges() {
        $('.select-all-module').each(function () {
            updateModuleBadge($(this).data('module'));
        });
    }

    // ── Form submit with loader ──────────────────────────────────
    $('#permissionsForm').on('submit', function () {
        var $btn = $('#savePermissionsBtn');
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');
    });

    @if(session('success'))
        showToast('{{ session('success') }}');
    @endif

});
</script>
@endsection
