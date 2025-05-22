<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <svg width="25" viewBox="0 0 25 42" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <path d="m0,40l22.5,0l-7.5,-12.5l7.5,-12.5l-22.5,0l0,25z" fill="#5a6acf"></path>
                        <path d="m0,20l7.5,-7.5l7.5,7.5l-7.5,7.5l-7.5,-7.5z" fill="#5a6acf"></path>
                        <path d="m22.5,0l-7.5,12.5l7.5,12.5l0,-25z" fill="#5a6acf"></path>
                    </g>
                </svg>
            </span>
            <span class="app-brand-text demo menu-text fw-bolder ms-2">{{ config('app.name') }}</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>

        @if(auth()->user()->hasPermission('manage_users') || auth()->user()->hasPermission('manage_roles'))
        <!-- User Management -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Management</span>
        </li>
        @endif

        @if(auth()->user()->hasPermission('manage_users'))
        <!-- Users -->
        <li class="menu-item {{ request()->routeIs('users.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Users">Users</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('users.index') ? 'active' : '' }}">
                    <a href="{{ route('users.index') }}" class="menu-link">
                        <div data-i18n="All Users">All Users</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('users.create') ? 'active' : '' }}">
                    <a href="{{ route('users.create') }}" class="menu-link">
                        <div data-i18n="Add User">Add User</div>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        @if(auth()->user()->hasPermission('manage_roles'))
        <!-- Roles & Permissions -->
        <li class="menu-item {{ request()->routeIs('roles.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-lock-open-alt"></i>
                <div data-i18n="Roles">Roles & Permissions</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('roles.index') ? 'active' : '' }}">
                    <a href="{{ route('roles.index') }}" class="menu-link">
                        <div data-i18n="All Roles">All Roles</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('roles.create') ? 'active' : '' }}">
                    <a href="{{ route('roles.create') }}" class="menu-link">
                        <div data-i18n="Add Role">Add Role</div>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        <!-- Content Management (contoh menu lain) -->
        @if(auth()->user()->hasPermission('create_content') || auth()->user()->hasPermission('edit_content'))
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Content</span>
        </li>
        
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div data-i18n="Content">Content</div>
            </a>
            <ul class="menu-sub">
                @if(auth()->user()->hasPermission('create_content'))
                <li class="menu-item">
                    <a href="#" class="menu-link">
                        <div data-i18n="Articles">Articles</div>
                    </a>
                </li>
                @endif
                @if(auth()->user()->hasPermission('edit_content'))
                <li class="menu-item">
                    <a href="#" class="menu-link">
                        <div data-i18n="Pages">Pages</div>
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        <!-- Reports -->
        @if(auth()->user()->hasPermission('view_reports'))
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Reports</span>
        </li>
        
        <li class="menu-item">
            <a href="#" class="menu-link">
                <i class="menu-icon tf-icons bx bx-chart"></i>
                <div data-i18n="Reports">Reports</div>
            </a>
        </li>
        @endif

        <!-- Settings -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Settings</span>
        </li>
        
        <li class="menu-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <a href="{{ route('profile.edit') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-cog"></i>
                <div data-i18n="Profile">Profile Settings</div>
            </a>
        </li>
    </ul>
</aside>