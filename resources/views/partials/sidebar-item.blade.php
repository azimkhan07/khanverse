@php

    $user = auth()->user();

    $userRole = strtolower($user->roleData?->name ?? '');

    // MENU ROLES
    $menuRoles = [];

    if ($item->roles) {
        $menuRoles = is_array($item->roles) ? $item->roles : json_decode($item->roles, true);

        $menuRoles = array_map('strtolower', $menuRoles);
    }

    // ROLE CHECK
    $canView = empty($menuRoles) || in_array($userRole, $menuRoles);

    // CHILD CHECK
    $hasChild = $item->children && $item->children->count() > 0;

    // FILTER CHILDREN
    $visibleChildren = $hasChild
        ? $item->children->filter(function ($child) use ($userRole) {
            $childRoles = [];

            if ($child->roles) {
                $childRoles = is_array($child->roles) ? $child->roles : json_decode($child->roles, true);

                $childRoles = array_map('strtolower', $childRoles);
            }

            return empty($childRoles) || in_array($userRole, $childRoles);
        })
        : collect();

    // IF PARENT HAS NO VISIBLE CHILD
    if ($hasChild && $visibleChildren->count() == 0) {
        $canView = false;
    }

    // ACTIVE MENU
    $isActive = false;

    if ($item->route_name) {
        $isActive = request()->routeIs($item->route_name);
    }

    // ACTIVE CHILD
    if (!$isActive && $visibleChildren->count()) {
        $isActive = $visibleChildren->contains(function ($child) {
            return $child->route_name && request()->routeIs($child->route_name);
        });
    }

@endphp


{{-- ================= SINGLE MENU ================= --}}
@if (!$hasChild && $canView)

    <li class="{{ $isActive ? 'active' : '' }}">

        <a href="{{ $item->route_name ? route($item->route_name) : 'javascript:void(0)' }}"
            class="waves-effect waves-dark">

            @if ($item->icon)
                <span class="pcoded-micon">
                    <i class="{{ $item->icon }}"></i>
                </span>
            @endif

            <span class="pcoded-mtext">
                {{ $item->title }}
            </span>

        </a>

    </li>

@endif



{{-- ================= SUB MENU ================= --}}
@if ($hasChild && ($canView || $visibleChildren->count()))

    <li class="pcoded-hasmenu {{ $isActive ? 'pcoded-trigger active' : '' }}">

        <a href="javascript:void(0)" class="waves-effect waves-dark">

            @if ($item->icon)
                <span class="pcoded-micon">
                    <i class="{{ $item->icon }}"></i>
                </span>
            @endif

            <span class="pcoded-mtext">
                {{ $item->title }}
            </span>

        </a>

        <ul class="pcoded-submenu">

            @foreach ($visibleChildren as $child)
                @include('partials.sidebar-item', [
                    'item' => $child,
                ])
            @endforeach

        </ul>

    </li>

@endif
