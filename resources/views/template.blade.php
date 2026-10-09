<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>E-SPP SYSTEM - Portal Administrasi</title>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('template/dist') }}/assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="{{ asset('template/dist') }}/assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="{{ asset('template/dist') }}/assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="{{ asset('template/dist') }}/assets/vendors/font-awesome/css/font-awesome.min.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet"
        href="{{ asset('template/dist') }}/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
    <!-- End plugin css for this page -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('template/dist') }}/assets/css/style.css">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="{{ asset('template/dist') }}/assets/images/favicon.png" />
    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.5/css/dataTables.dataTables.min.css">
    {{-- sweetalert --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    {{-- bootstrap icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- fontawesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    {{-- DataTables Responsive CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">

    {{-- Modern Admin Theme (Navy & Orange Brand) --}}
    <link rel="stylesheet" href="{{ asset('css/admin-theme.css') }}">
    @stack('css')
</head>

<body class="admin-theme">
    <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        @include('layouts.navbar')
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:partials/_sidebar.html -->
            @include('layouts.sidebar')
            <!-- partial -->
            <div class="main-panel">
                @yield('content')
                <!-- content-wrapper ends -->
                <!-- partial:partials/_footer.html -->
                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->

        <!-- Mobile Sidebar Backdrop Overlay -->
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
    </div>

    <!-- AI SPP Agent Modal & Floating Trigger -->
    @include('components.ai-agent-modal')

    <!-- plugins:js -->
    <script src="{{ asset('template/dist') }}/assets/vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="{{ asset('template/dist') }}/assets/vendors/chart.js/chart.umd.js"></script>
    <script src="{{ asset('template/dist') }}/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{ asset('template/dist') }}/assets/js/off-canvas.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/misc.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/settings.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/todolist.js"></script>
    <script src="{{ asset('template/dist') }}/assets/js/jquery.cookie.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="{{ asset('template/dist') }}/assets/js/dashboard.js"></script>
    <!-- End custom js for this page -->
    {{-- DataTables JS --}}
    <script src="https://cdn.datatables.net/2.3.5/js/dataTables.min.js"></script>
    {{-- sweetalert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- DataTables Responsive JS --}}
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>

    {{-- Mobile Sidebar Drawer Backdrop Script --}}
    <script>
        (function ($) {
            'use strict';
            $(function () {
                var $sidebar = $('.sidebar-offcanvas');
                var $backdrop = $('#sidebarBackdrop');
                var $body = $('body');

                function updateSidebarState() {
                    if ($sidebar.hasClass('active')) {
                        $backdrop.addClass('show');
                        $body.addClass('sidebar-open');
                    } else {
                        $backdrop.removeClass('show');
                        $body.removeClass('sidebar-open');
                    }
                }

                function closeMobileSidebar() {
                    $sidebar.removeClass('active');
                    $backdrop.removeClass('show');
                    $body.removeClass('sidebar-open');
                }

                $('[data-toggle="offcanvas"]').on('click', function () {
                    // Check if sidebar has active class after off-canvas.js toggles it
                    setTimeout(updateSidebarState, 15);
                });

                $backdrop.on('click', function () {
                    closeMobileSidebar();
                });

                $(document).on('click', '#sidebarMobileCloseBtn', function (e) {
                    e.preventDefault();
                    closeMobileSidebar();
                });

                // Close drawer on ESC key
                $(document).on('keydown', function (e) {
                    if (e.key === 'Escape' && $sidebar.hasClass('active')) {
                        closeMobileSidebar();
                    }
                });

                // Auto close mobile drawer on link navigation
                $(document).on('click', '.sidebar-offcanvas .nav-link', function () {
                    if ($(window).width() < 992 && !$(this).attr('data-bs-toggle')) {
                        closeMobileSidebar();
                    }
                });

                // Reset on window resize to desktop
                $(window).on('resize', function () {
                    if ($(window).width() >= 992) {
                        closeMobileSidebar();
                    }
                });
            });
        })(jQuery);
    </script>
    @stack('scripts')
</body>

</html>
