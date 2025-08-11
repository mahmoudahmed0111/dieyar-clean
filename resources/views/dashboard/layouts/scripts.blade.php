    <!-- Uppy JS -->
    <script src="https://releases.transloadit.com/uppy/v3.25.2/uppy.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Toggle for Mobile
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebar-toggle');
            const overlay = document.getElementById('sidebar-overlay');

            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', () => {
                    sidebar.classList.toggle('open');
                    if (overlay) {
                        overlay.classList.toggle('show');
                    }
                });
            }

            // Close sidebar when clicking overlay
            if (overlay) {
                overlay.addEventListener('click', () => {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('show');
                });
            }

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', (e) => {
                if (window.innerWidth <= 768) {
                    if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target) && !overlay.contains(e.target)) {
                        sidebar.classList.remove('open');
                        if (overlay) {
                            overlay.classList.remove('show');
                        }
                    }
                }
            });

            // Handle window resize
            window.addEventListener('resize', () => {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('open');
                    if (overlay) {
                        overlay.classList.remove('show');
                    }
                }
            });

            // Language Switcher
            const languageToggle = document.getElementById('language-toggle');
            const languageDropdown = document.getElementById('language-dropdown');

            if (languageToggle && languageDropdown) {
                languageToggle.addEventListener('click', (e) => {
                    e.stopPropagation();
                    languageDropdown.classList.toggle('show');
                });

                // Close language dropdown when clicking outside
                document.addEventListener('click', () => {
                    languageDropdown.classList.remove('show');
                });
            }

            // User Dropdown
            const userAvatarBtn = document.querySelector('.user-avatar-btn');
            const dropdownMenu = document.querySelector('.dropdown-menu');

            if (userAvatarBtn && dropdownMenu) {
                userAvatarBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    dropdownMenu.classList.toggle('show');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', () => {
                    dropdownMenu.classList.remove('show');
                });
            }

            // Notification Badge Animation
            const notificationBtn = document.querySelector('.notification-btn');
            if (notificationBtn) {
                notificationBtn.addEventListener('click', () => {
                    // Add pulse animation
                    notificationBtn.classList.add('pulse');
                    setTimeout(() => {
                        notificationBtn.classList.remove('pulse');
                    }, 1000);
                });
            }

            // Active Navigation Highlighting
            const navItems = document.querySelectorAll('.nav-item');
            navItems.forEach(item => {
                item.addEventListener('click', () => {
                    navItems.forEach(nav => nav.classList.remove('active'));
                    item.classList.add('active');
                });
            });

            // Auto-hide notifications after 5 seconds
            const notifications = document.querySelectorAll('.notification');
            notifications.forEach(notification => {
                setTimeout(() => {
                    notification.style.opacity = '0';
                    setTimeout(() => {
                        notification.remove();
                    }, 300);
                }, 5000);
            });

            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Loading states for buttons
            document.querySelectorAll('.btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (!this.classList.contains('loading')) {
                        this.classList.add('loading');
                        const originalText = this.textContent;
                        this.innerHTML = '<span class="spinner"></span> جاري التحميل...';

                        // Simulate loading (remove in production)
                        setTimeout(() => {
                            this.classList.remove('loading');
                            this.textContent = originalText;
                        }, 2000);
                    }
                });
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', (e) => {
                // Ctrl/Cmd + K to toggle sidebar
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    sidebar.classList.toggle('open');
                }

                // Escape to close modals/dropdowns
                if (e.key === 'Escape') {
                    dropdownMenu?.classList.remove('show');
                    languageDropdown?.classList.remove('show');
                    sidebar.classList.remove('open');
                    if (overlay) {
                        overlay.classList.remove('show');
                    }
                }
            });

            // Header scroll effect
            const header = document.querySelector('.header');
            if (header) {
                window.addEventListener('scroll', () => {
                    if (window.scrollY > 10) {
                        header.classList.add('scrolled');
                    } else {
                        header.classList.remove('scrolled');
                    }
                });
            }

            // Auto-refresh system status every 30 seconds
            setInterval(() => {
                // Update timestamp in footer
                const timestamp = document.querySelector('.footer-section p');
                if (timestamp) {
                    const now = new Date();
                    const locale = '{{ app()->getLocale() }}';
                    const options = {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit'
                    };
                    timestamp.textContent =
                        `${locale === 'ar' ? 'آخر تحديث' : 'Last Update'}: ${now.toLocaleDateString(locale === 'ar' ? 'ar-SA' : 'en-US', options)}`;
                }
            }, 30000);

            // Handle window resize
            window.addEventListener('resize', () => {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('open');
                    if (overlay) {
                        overlay.classList.remove('show');
                    }
                }
            });
        });
    </script>

    @if (Session::has('success') || Session::has('error'))
        <script>
            Swal.mixin({
                toast: !0,
                position: "{{ app()->getLocale() == 'ar' ? 'top-start' : 'top-end' }}",
                showConfirmButton: !1,
                timer: 3e3,
                timerProgressBar: !0,
                didOpen: (e) => {
                    e.addEventListener("mouseenter", Swal.stopTimer),
                        e.addEventListener("mouseleave", Swal.resumeTimer);
                },
            }).fire({
                text: "{{ Session::has('success') ? Session::get('success') : Session::get('error') }}",
                icon: "{{ Session::has('success') ? 'success' : 'error' }}",
            });
        </script>
    @endif
    <script>
        $(document).ready(function() {
            $('.loader').fadeOut(500, function() {
                $('.overlay-loader').fadeOut(function() {
                    $(this).remove();
                });
            });
        });

        try {
            new simpleDatatables.DataTable("#dataTable", {
                searchable: !0,
                fixedHeight: !1,
            });
        } catch (e) {}
    </script>
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.GLightbox) {
                GLightbox({ selector: '.glightbox' });
            }
        });
    </script>
