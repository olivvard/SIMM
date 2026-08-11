<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description"
    content="viho admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
<meta name="keywords"
    content="admin template, viho admin template, dashboard template, flat admin template, responsive admin template, web app">
<meta name="author" content="pixelstrap">
<link rel="icon" href="{{ asset('weai-ori.png') }}" type="image/x-icon">
<link rel="shortcut icon" href="{{ asset('weai-ori.png') }}" type="image/x-icon">
<title>WEA - Monitoring Tools</title>
<!-- Google font-->
<link rel="preconnect" href="https://fonts.gstatic.com">
<link
    href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap"
    rel="stylesheet">
<link
    href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&amp;display=swap"
    rel="stylesheet">
<link
    href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap"
    rel="stylesheet">
<!-- Font Awesome-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/fontawesome.css') }}">
<!-- ico-font-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/icofont.css') }}">
<!-- Themify icon-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/themify.css') }}">
<!-- Flag icon-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/flag-icon.css') }}">
<!-- Feather icon-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/feather-icon.css') }}">
<!-- Plugins css start-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/animate.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/chartist.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/date-picker.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/prism.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vector-map.css') }}">
<!-- Plugins css Ends-->
<!-- Bootstrap css-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap.css') }}">
<!-- App css-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
<link id="color" rel="stylesheet" href="{{ asset('assets/css/color-1.css') }}" media="screen">
<!-- Responsive css-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive.css') }}">

<!-- Custom overrides -->
<style>
    .badge-location {
        background-color: #eff6ff !important;
        color: #2563eb !important;
        border: 1px solid #bfdbfe !important;
        font-weight: 600;
        padding: 5px 10px;
        border-radius: 6px;
        display: inline-block;
    }

    /* Fix Sidebar Glitch / Squished Layout on Medium and Mobile Screens */
    @media (max-width: 991.98px) {
        body .page-wrapper.compact-wrapper .page-body-wrapper header.main-nav {
            width: 290px !important;
            left: -290px !important;
            top: 65px !important;
            opacity: 0 !important;
            visibility: hidden !important;
            z-index: 1000 !important;
            transition: left 0.3s ease, opacity 0.3s ease, visibility 0.3s ease !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15) !important;
        }

        body .page-wrapper.compact-wrapper .page-body-wrapper header.main-nav.close_icon {
            left: -290px !important;
            opacity: 0 !important;
            visibility: hidden !important;
        }

        body .page-wrapper.compact-wrapper .page-body-wrapper header.main-nav:not(.close_icon) {
            left: 0 !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        /* Restore centered profile layout instead of squished floated layout */
        body .page-wrapper.compact-wrapper .page-body-wrapper header .sidebar-user {
            padding: 30px 20px !important;
            text-align: center !important;
        }

        body .page-wrapper.compact-wrapper .page-body-wrapper header .sidebar-user img {
            width: 90px !important;
            height: 90px !important;
            float: none !important;
            margin: 0 auto !important;
            border-width: 4px !important;
            object-fit: cover !important;
        }

        body .page-wrapper.compact-wrapper .page-body-wrapper header .sidebar-user h6 {
            margin-top: 15px !important;
            padding-left: 0 !important;
            text-align: center !important;
        }

        body .page-wrapper.compact-wrapper .page-body-wrapper header .sidebar-user p {
            padding-left: 0 !important;
            text-align: center !important;
            max-width: none !important;
        }
        
        body .page-wrapper.compact-wrapper .page-body-wrapper header .main-navbar .nav-menu {
            height: calc(100vh - 280px) !important;
        }
    }
</style>