<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SPP</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('template/dist') }}/assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="{{ asset('template/dist') }}/assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="{{ asset('template/dist') }}/assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="{{ asset('template/dist') }}/assets/vendors/font-awesome/css/font-awesome.min.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{ asset('template/dist') }}/assets/vendors/font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet"
        href="{{ asset('template/dist') }}/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('template/dist') }}/assets/css/style.css">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="{{ asset('template/dist') }}/assets/images/favicon.png" />
    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.5/css/dataTables.dataTables.min.css">
    {{-- sweetalert --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    {{-- <link rel="stylesheet" href="css/style.css"> --}}
    @stack('style')
</head>

<body>
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
    </div>
    <!-- container-scroller -->
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
    @stack('scripts')
</body>

</html>
