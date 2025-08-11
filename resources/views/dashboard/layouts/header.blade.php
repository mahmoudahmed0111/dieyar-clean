<header class="header">
    <!-- Left Section: Menu Toggle & Brand -->
    <div class="header-left">
        <button id="sidebar-toggle" class="menu-toggle" aria-label="{{ __('Toggle Sidebar') }}">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <div class="header-brand">
            <span class="brand-text">Kareem Clean</span>
        </div>
    </div>

    <!-- Right Section: User Info & Actions -->
    <div class="header-right">

        <!-- User Actions -->
        <div class="user-actions">
            <!-- Language Switcher -->
            <div class="language-switcher">
                <button class="language-btn" id="language-toggle" aria-label="{{ __('Change Language') }}">
                    <span
                        class="language-text">{{ app()->getLocale() == 'ar' ? __('trans.arabic') : __('trans.english') }}</span>

                </button>
                <div class="language-dropdown" id="language-dropdown">
                    <a href="{{ route('language.switch', 'ar') }}"
                        class="language-option {{ app()->getLocale() == 'ar' ? 'active' : '' }}">
                        <span class="flag">🇸🇦</span>
                        <span class="language-name">{{ __('trans.arabic') }}</span>
                    </a>
                    <a href="{{ route('language.switch', 'en') }}"
                        class="language-option {{ app()->getLocale() == 'en' ? 'active' : '' }}">
                        <span class="flag">🇺🇸</span>
                        <span class="language-name">{{ __('trans.english') }}</span>
                    </a>
                </div>
            </div>

            <!-- Notifications -->
            <div class="notification-dropdown-container" style="position: relative;">
                <button class="notification-btn" id="notification-toggle" aria-label="{{ __('trans.notifications') }}">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="notification-badge">3</span>
                </button>
                <div class="dropdown-menu" id="notification-dropdown">
                    <div class="dropdown-header">{{ __('trans.notifications') }}</div>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-item"> {{ __('trans.new_user_registered') }}</div>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-item"> {{ __('trans.chalet_no_3_cleaning_completed') }}</div>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-item"> {{ __('trans.new_maintenance_request_for_chalet_no_1') }}</div>
                </div>
            </div>

            <!-- User Avatar & Dropdown -->
            <div class="user-dropdown">
                <button class="user-avatar-btn" id="user-menu-toggle" aria-label="{{ __('trans.user_menu') }}">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=2563eb&color=fff&size=40"
                        alt="{{ Auth::user()->name }}" class="user-avatar">

                </button>
                <div class="dropdown-menu" id="user-menu-dropdown">
                    <div class="dropdown-header">
                        <span class="dropdown-user-name">{{ Auth::user()->name }}</span>
                        <span class="dropdown-user-role">{{ __('trans.system_manager') }}</span>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>{{ __('trans.profile') }}</span>
                    </a>
                    <a href="#" class="dropdown-item">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>{{ __('trans.settings') }}</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}" class="dropdown-form">
                        @csrf
                        <button type="submit" class="dropdown-item dropdown-logout">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span>{{ __('trans.logout') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Notification dropdown
            const notificationBtn = document.getElementById('notification-toggle');
            const notificationDropdown = document.getElementById('notification-dropdown');

            if (notificationBtn && notificationDropdown) {
                notificationBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    notificationDropdown.classList.toggle('show');
                    // Close user menu if open
                    const userMenuDropdown = document.getElementById('user-menu-dropdown');
                    if (userMenuDropdown) {
                        userMenuDropdown.classList.remove('show');
                    }
                });
            }

            // User menu dropdown
            const userMenuBtn = document.getElementById('user-menu-toggle');
            const userMenuDropdown = document.getElementById('user-menu-dropdown');

            if (userMenuBtn && userMenuDropdown) {
                userMenuBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userMenuDropdown.classList.toggle('show');
                    // Close notification dropdown if open
                    if (notificationDropdown) {
                        notificationDropdown.classList.remove('show');
                    }
                });
            }

            // Close dropdowns when clicking outside
            document.addEventListener('click', function(e) {
                if (notificationDropdown && !notificationDropdown.contains(e.target) && e.target !== notificationBtn) {
                    notificationDropdown.classList.remove('show');
                }
                if (userMenuDropdown && !userMenuDropdown.contains(e.target) && e.target !== userMenuBtn) {
                    userMenuDropdown.classList.remove('show');
                }
            });
        });
    </script>
@append
