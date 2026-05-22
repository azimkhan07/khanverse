@php

    $menus = getSidebarMenu();

    $parents = $menus->whereNull('parent_id');

@endphp

<nav class="pcoded-navbar">

    <div class="nav-list">

        <div class="pcoded-inner-navbar main-menu">

            <div class="pcoded-navigation-label">
                Navigation
            </div>

            <ul class="pcoded-item pcoded-left-item">

                @foreach ($parents as $menu)
                    @include('partials.sidebar-item', [
                        'item' => $menu,
                    ])
                @endforeach

            </ul>

        </div>

    </div>

</nav>