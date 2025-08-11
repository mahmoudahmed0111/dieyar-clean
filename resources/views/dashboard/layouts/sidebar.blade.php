<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
            </svg>
            <h2>{{ __('trans.Dashboard') }}</h2>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">
            <h3 class="nav-section-title">{{ __('trans.main_menu') }}</h3>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa fa-home"></i>
                <span>{{ __('trans.home') }}</span>
            </a>
            <a href="{{ route('dashboard.users.index') }}"
                class="nav-item {{ request()->routeIs('dashboard.users.*') ? 'active' : '' }}">
                <i class="fa fa-users"></i>
                <span>{{ __('trans.users') }}</span>
            </a>

            <a href="{{ route('dashboard.cleaners.index') }}"
                class="nav-item {{ request()->routeIs('dashboard.cleaners.*') ? 'active' : '' }}">
                <i class="fa fa-broom"></i>
                <span>{{ __('trans.cleaners') }}</span>
            </a>

        </div>

        <div class="nav-section">
            <h3 class="nav-section-title">{{ __('trans.service_management') }}</h3>
            <a href="{{ route('dashboard.chalets.index') }}"
                class="nav-item {{ request()->routeIs('dashboard.chalets.*') ? 'active' : '' }}">
                <i class="fa fa-bed"></i>
                <span>{{ __('trans.chalets') }}</span>
            </a>
            <a href="{{ route('dashboard.damages.index') }}" class="nav-item {{ request()->routeIs('dashboard.damages.*') ? 'active' : '' }}">
                <i class="fa fa-exclamation-triangle"></i>
                <span>{{ __('trans.damages') }}</span>
            </a>
            <a href="{{ route('dashboard.deep_cleanings.index') }}" class="nav-item {{ request()->routeIs('dashboard.deep_cleanings.*') ? 'active' : '' }}">
                <i class="fa fa-soap"></i>
                <span>{{ __('trans.deep_cleanings') }}</span>
            </a>
            <a href="{{ route('dashboard.regular_cleanings.index') }}" class="nav-item {{ request()->routeIs('dashboard.regular_cleanings.*') ? 'active' : '' }}">
                <i class="fa fa-spray-can"></i>
                <span>{{ __('trans.regular_cleanings') }}</span>
            </a>
            <a href="{{ route('dashboard.maintenance.index') }}" class="nav-item {{ request()->routeIs('dashboard.maintenance.*') ? 'active' : '' }}    ">
                <i class="fa fa-tools"></i>
                    <span>{{ __('trans.maintenance') }}</span>
            </a>
            <a href="{{ route('dashboard.pest_controls.index') }}" class="nav-item {{ request()->routeIs('dashboard.pest_controls.*') ? 'active' : '' }}">
                <i class="fa fa-bug"></i>
                <span>{{ __('trans.pest_controls') }}</span>
            </a>
        </div>

        <div class="nav-section">
            <h3 class="nav-section-title">{{ __('trans.inventory_reports') }}</h3>
            <a href="{{ route('dashboard.inventory.index') }}" class="nav-item {{ request()->routeIs('dashboard.inventory.*') ? 'active' : '' }}    ">
                <i class="fa fa-box"></i>
                <span>{{ __('trans.inventory') }}</span>
            </a>

        </div>

        <div class="nav-section">
            <h3 class="nav-section-title">{{ __('trans.settings') }}</h3>
            <a href="{{ route('dashboard.settings.index') }}" class="nav-item {{ request()->routeIs('dashboard.settings.*') ? 'active' : '' }}">
                <i class="fa fa-cogs"></i>
                <span>{{ __('trans.settings') }}</span>
            </a>
        </div>
    </nav>

    <div class="sidebar-footer">
        <div class="system-status">
            <div class="status-indicator online"></div>
            <span>{{ __('trans.system_online') }}</span>
        </div>
        <div class="copyright">
            &copy; {{ date('Y') }} {{ $settings->name }}
        </div>
    </div>
</aside>
