<!DOCTYPE html>
<html lang="en">

<head>
    @include('components.head-css')
</head>

<body>
    <div class="page-wrapper compact-wrapper" id="pageWrapper">
        @include('components.navbar')
        <div class="page-body-wrapper sidebar-icon">
            @include('components.sidebar')
            <div class="page-body">
                <div class="container-fluid dashboard-default-sec">
                    @yield('content')
                </div>
            </div>
            @include('components.footer')
        </div>
    </div>
    @include('components.vendor')
    @stack('scripts')
</body>

</html>