<!-- latest jquery-->
    <script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/theme-customizer/customizer.js') }}"></script>
    <!-- feather icon js-->
    <script src="{{ asset('assets/js/icons/feather-icon/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/icons/feather-icon/feather-icon.js') }}"></script>
    <!-- Sidebar jquery-->
    <script src="{{ asset('assets/js/sidebar-menu.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <!-- Bootstrap js-->
    <script src="{{ asset('assets/js/bootstrap/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap/bootstrap.min.js') }}"></script>
    <!-- Plugins JS start-->
    <script src="{{ asset('assets/js/chart/chartist/chartist.js') }}"></script>
    <script src="{{ asset('assets/js/chart/chartist/chartist-plugin-tooltip.js') }}"></script>
    <script src="{{ asset('assets/js/chart/knob/knob.min.js') }}"></script>
    <script src="{{ asset('assets/js/chart/knob/knob-chart.js') }}"></script>
    <script src="{{ asset('assets/js/chart/apex-chart/apex-chart.js') }}"></script>
    <script src="{{ asset('assets/js/chart/apex-chart/stock-prices.js') }}"></script>
    <script src="{{ asset('assets/js/prism/prism.min.js') }}"></script>
    <script src="{{ asset('assets/js/clipboard/clipboard.min.js') }}"></script>
    <script src="{{ asset('assets/js/counter/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('assets/js/counter/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('assets/js/counter/counter-custom.js') }}"></script>
    <script src="{{ asset('assets/js/custom-card/custom-card.js') }}"></script>
    <script src="{{ asset('assets/js/notify/bootstrap-notify.min.js') }}"></script>
    <script src="{{ asset('assets/js/vector-map/jquery-jvectormap-2.0.2.min.js') }}"></script>
    <script src="{{ asset('assets/js/vector-map/map/jquery-jvectormap-world-mill-en.js') }}"></script>
    <script src="{{ asset('assets/js/vector-map/map/jquery-jvectormap-us-aea-en.js') }}"></script>
    <script src="{{ asset('assets/js/vector-map/map/jquery-jvectormap-uk-mill-en.js') }}"></script>
    <script src="{{ asset('assets/js/vector-map/map/jquery-jvectormap-au-mill.js') }}"></script>
    <script src="{{ asset('assets/js/vector-map/map/jquery-jvectormap-chicago-mill-en.js') }}"></script>
    <script src="{{ asset('assets/js/vector-map/map/jquery-jvectormap-in-mill.js') }}"></script>
    <script src="{{ asset('assets/js/vector-map/map/jquery-jvectormap-asia-mill.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard/default.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.en.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.custom.js') }}"></script>
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <!-- login js-->
    <!-- Plugin used-->

    {{-- ── Mobile Sidebar Burger Fix ──────────────────────────────────────────
         Template Viho hanya men-handle .toggle-sidebar (desktop) dan
         .mobile-toggle (hamburger kanan atas untuk nav-menus, bukan sidebar).
         Fix ini menghubungkan tombol burger mobile (#mobile-sidebar-toggle)
         ke main-nav, sehingga sidebar benar-benar terbuka/tutup di mobile.
    ──────────────────────────────────────────────────────────────────────── --}}
    <script>
    (function() {
        var mobileBtn = document.getElementById('mobile-sidebar-toggle');
        var mainNav   = document.querySelector('.main-nav');
        var overlay   = document.createElement('div');

        // Overlay gelap di belakang sidebar saat mobile
        overlay.id = 'sidebar-overlay';
        overlay.style.cssText = [
            'display:none',
            'position:fixed',
            'inset:0',
            'background:rgba(0,0,0,0.45)',
            'z-index:999',
            'transition:opacity 0.25s'
        ].join(';');
        document.body.appendChild(overlay);

        function openSidebar() {
            if (!mainNav) return;
            mainNav.classList.remove('close_icon');
            mainNav.style.left = '0';
            overlay.style.display = 'block';
        }

        function closeSidebar() {
            if (!mainNav) return;
            mainNav.classList.add('close_icon');
            mainNav.style.left = '';
            overlay.style.display = 'none';
        }

        // Mobile burger → buka sidebar
        if (mobileBtn) {
            mobileBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                var isOpen = mainNav && !mainNav.classList.contains('close_icon');
                isOpen ? closeSidebar() : openSidebar();
            });
        }

        // Klik overlay → tutup sidebar
        overlay.addEventListener('click', closeSidebar);

        // Klik link di dalam sidebar → tutup sidebar otomatis (mobile UX)
        if (mainNav) {
            mainNav.querySelectorAll('a.nav-link').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 992) closeSidebar();
                });
            });

            // Tombol "Back" di dalam sidebar mobile
            var backBtn = mainNav.querySelector('.mobile-back');
            if (backBtn) backBtn.addEventListener('click', closeSidebar);
        }

        // Tutup sidebar otomatis saat resize ke desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992) {
                overlay.style.display = 'none';
                if (mainNav) mainNav.style.left = '';
            }
        });
    })();
    </script>