<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <!-- Brand/Logo Section -->
    <div class="app-brand demo">
        <a href="{{ route('dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <!-- Beautiful Logo SVG -->
                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="32" height="32" rx="8" fill="url(#paint0_linear_1_1)"/>
                    <path d="M12 8H20C21.1046 8 22 8.89543 22 10V14C22 15.1046 21.1046 16 20 16H16V20C16 21.1046 15.1046 22 14 22H10C8.89543 22 8 21.1046 8 20V12C8 9.79086 9.79086 8 12 8Z" fill="white"/>
                    <circle cx="18" cy="18" r="3" fill="white" fill-opacity="0.8"/>
                    <defs>
                        <linearGradient id="paint0_linear_1_1" x1="0" y1="0" x2="32" y2="32" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#696CFF"/>
                            <stop offset="1" stop-color="#9155FD"/>
                        </linearGradient>
                    </defs>
                </svg>
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-2" style="color: #566a7f; font-size: 1.125rem;">
                {{ config('app.name', 'Admin Panel') }}
            </span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <!-- Menu Items -->
    <ul class="menu-inner py-1">
        
        <!-- Dashboard -->
        <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-smile" style="color: #696cff;"></i>
                <div data-i18n="Dashboard" class="fw-medium">Dashboard</div>
            </a>
        </li>

        <!-- Spacer -->
        <li class="menu-header small text-uppercase mt-3">
            <span class="menu-header-text" style="color: #a8aaae; font-weight: 600; letter-spacing: 0.5px;">
                Management
            </span>
        </li>

        @if(auth()->user()->hasPermission('manage_users'))
        <!-- Users Management -->
        <li class="menu-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <a href="{{ route('users.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user-circle" style="color: #28c76f;"></i>
                <div data-i18n="Users" class="fw-medium">Users Management</div>
                <div class="badge badge-center rounded-pill bg-label-success ms-auto">
                    {{ \App\Models\User::count() }}
                </div>
            </a>
        </li>
        @endif

        @if(auth()->user()->hasPermission('manage_roles'))
        <!-- Roles & Permissions -->
        <li class="menu-item {{ request()->routeIs('roles.*') ? 'active' : '' }}">
            <a href="{{ route('roles.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-shield-quarter" style="color: #ff3e1d;"></i>
                <div data-i18n="Roles" class="fw-medium">Roles & Permissions</div>
                <div class="badge badge-center rounded-pill bg-label-danger ms-auto">
                    {{ \App\Models\Role::count() }}
                </div>
            </a>
        </li>
        @endif

        <!-- Spacer Bottom -->
        <li class="menu-header small text-uppercase mt-4">
            <span class="menu-header-text" style="color: #a8aaae; font-weight: 600; letter-spacing: 0.5px;">
                Account
            </span>
        </li>

        <!-- Profile Settings -->
        <li class="menu-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <a href="{{ route('profile.edit') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user-check" style="color: #8A8D93;"></i>
                <div data-i18n="Profile" class="fw-medium">Profile Settings</div>
            </a>
        </li>

        <!-- Logout -->
        <li class="menu-item">
            <a href="javascript:void(0)" class="menu-link" onclick="document.getElementById('logout-form').submit();">
                <i class="menu-icon tf-icons bx bx-power-off" style="color: #ff4757;"></i>
                <div data-i18n="Logout" class="fw-medium">Logout</div>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </li>

    </ul>

    <!-- User Info Card at Bottom -->
    <div class="ps-3 pe-3 pb-3 mt-auto">
        <div class="card bg-gradient-primary text-white" style="background: linear-gradient(135deg, #696cff 0%, #9155fd 100%); border: none; border-radius: 12px;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-2">
                        <img src="{{ asset('sneat/assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle" style="border: 2px solid rgba(255,255,255,0.3);">
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0 text-white" style="font-size: 0.875rem;">{{ auth()->user()->name }}</h6>
                        <small class="text-white-50" style="font-size: 0.75rem;">{{ auth()->user()->role->display_name ?? 'User' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</aside>