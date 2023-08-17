<!DOCTYPE html>

<html lang="it">
<!--begin::Head-->
<head>
    <base href=""/>
    <meta charset="utf-8"/>
    <title>{{$titoloPagina??config('configurazione.tag_title')}}</title>
    <meta name="description" content=""/>
    <meta name="keywords" content=""/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="_token" content="{{csrf_token()}}">
    <link rel="shortcut icon" href="/assets_demo46/media/logos/favicon.ico"/>

    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700"/><!--end::Fonts-->

    <!--begin::Vendor Stylesheets(used for this page only)-->
    <link href="/assets_demo46/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css"/>
    <!--end::Vendor Stylesheets-->


    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="/assets_demo46/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css"/>
    <link href="/assets_demo46/css/style.bundle.css" rel="stylesheet" type="text/css"/>
    <!--end::Global Stylesheets Bundle-->


    <script>
        // Frame-busting to prevent site from being loaded within a frame without permission (click-jacking)
        if (window.top != window.self) {
            window.top.location.replace(window.self.location.href);
        }
    </script>
</head>
<!--end::Head-->

<!--begin::Body-->
<body id="kt_app_body" data-kt-app-header-fixed="true" data-kt-app-header-fixed-mobile="true"
      data-kt-app-toolbar-enabled="true" class="safari-mode app-default">
<!--begin::Theme mode setup on page load-->
<script>
    var defaultThemeMode = "light";
    var themeMode;

    if (document.documentElement) {
        if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
            themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
        } else {
            if (localStorage.getItem("data-bs-theme") !== null) {
                themeMode = localStorage.getItem("data-bs-theme");
            } else {
                themeMode = defaultThemeMode;
            }
        }

        if (themeMode === "system") {
            themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
        }

        document.documentElement.setAttribute("data-bs-theme", themeMode);
    }
</script>
<!--end::Theme mode setup on page load-->

<!--begin::App-->
<div class="d-flex flex-column flex-root app-root" id="kt_app_root">
    <!--begin::Page-->
    <div class="app-page  flex-column flex-column-fluid " id="kt_app_page">
        <!--begin::Header-->
        <div id="kt_app_header" class="app-header ">

            <!--begin::Header container-->
            <div class="app-container  container-fluid d-flex align-items-stretch justify-content-between "
                 id="kt_app_header_container">
                <!--begin::Header mobile toggle-->
                <div class="d-flex align-items-center d-lg-none ms-n2 me-2" title="Show sidebar menu">
                    <div class="btn btn-icon btn-color-white btn-active-color-primary w-35px h-35px"
                         id="kt_app_sidebar_mobile_toggle">
                        <i class="ki-duotone ki-abstract-14 fs-2"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <!--end::Header mobile toggle-->

                <!--begin::Logo-->
                <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0 me-5 me-lg-0">
                    <a href="/">
                        <img alt="Logo" src="/loghi/logo-ag-white.png" class="d-none d-sm-block mw-80px"/>
                        <img alt="Logo" src="/loghi/logo-ag-blu.png" class="d-block d-sm-none mw-80px"/>
                    </a>
                </div>
                <!--end::Logo-->

                <!--begin::Header wrapper-->
                <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1"
                     id="kt_app_header_wrapper">

                    <!--begin::Menu wrapper-->
                    @include('Frontend._layout.app-header-menu')
                    <!--end::Menu wrapper-->

                    <!--begin::Navbar-->
                    <div class="app-navbar flex-shrink-0">


                        @includeWhen(false,'Frontend._layout.altriMenu')
                        <!--begin::User menu-->
                        @include('Frontend._layout.user-menu')
                        <!--end::User menu-->

                        <!--begin::Header menu toggle-->
                        <div class="app-navbar-item d-lg-none ms-2 me-n2" title="Show header menu">
                            <div
                                class="btn btn-icon btn-color-white btn-active-color-primary w-30px h-30px w-md-35px h-md-35px"
                                id="kt_app_header_menu_toggle">
                                <i class="ki-duotone ki-text-align-left fs-2 fs-md-1 fw-bold"><span
                                        class="path1"></span><span class="path2"></span><span class="path3"></span><span
                                        class="path4"></span></i></div>
                        </div>
                        <!--end::Header menu toggle-->
                    </div>
                    <!--end::Navbar-->
                </div>
                <!--end::Header wrapper-->
            </div>
            <!--end::Header container-->
        </div>
        <!--end::Header-->
        <!--begin::Wrapper-->
        <div class="app-wrapper  d-flex " id="kt_app_wrapper">


            <!--begin::Wrapper container-->
            <div class="app-container  container-fluid ">


                <!--begin::Main-->
                <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                    <!--begin::Content wrapper-->
                    <div class="d-flex flex-column flex-column-fluid">

                        <!--begin::Toolbar-->
                        <div id="kt_app_toolbar" class="app-toolbar "

                        >

                            <!--begin::Toolbar container-->
                            <div class="d-flex flex-stack flex-row-fluid">
                                <!--begin::Toolbar wrapper-->
                                <div class="d-flex flex-column flex-row-fluid">

                                    <!--begin::Breadcrumb-->
                                    @includeWhen(isset($breadcrumbs),'Frontend._layout.breadcrumbs')
                                    <!--end::Breadcrumb-->


                                    <!--begin::Page title-->
                                    <div class="page-title d-flex align-items-center me-3">
                                        <!--begin::Title-->
                                        <h1 class="page-heading d-flex text-dark fw-bolder fs-1 flex-column justify-content-center my-0">
                                            {{$titoloPagina??''}}
                                        </h1>
                                        <!--end::Title-->
                                    </div>
                                    <!--end::Page title-->
                                </div>
                                <!--end::Toolbar wrapper-->

                                <!--begin::Actions-->
                                <div class="d-flex align-items-center gap-3 gap-lg-5">
                                    @yield('toolbar')
                                </div>
                                <!--end::Actions-->
                            </div>
                            <!--end::Toolbar container-->
                        </div>
                        <!--end::Toolbar-->

                        <!--begin::Content-->
                        <div id="kt_app_content" class="app-content ">

                            @yield('content')

                        </div>
                        <!--end::Content-->
                    </div>
                    <!--end::Content wrapper-->


                    <!--begin::Footer-->
                    <div id="kt_app_footer"
                         class="app-footer  d-flex flex-column flex-md-row align-items-center flex-center flex-md-stack ">
                        <!--begin::Copyright-->
                        <div class="text-dark order-2 order-md-1">
                            <span class="text-gray-400 fw-semibold me-1">2023&copy;</span>
                            <a href="https://keenthemes.com" target="_blank" class="text-gray-400 text-hover-primary">Keenthemes</a>
                        </div>
                        <!--end::Copyright-->

                        <!--begin::Menu-->
                        <ul class="menu menu-gray-400 menu-hover-primary fw-semibold order-1">
                            <li class="menu-item"><a href="https://keenthemes.com" target="_blank"
                                                     class="menu-link px-2">About</a></li>

                            <li class="menu-item"><a href="https://devs.keenthemes.com" target="_blank"
                                                     class="menu-link px-2">Support</a></li>

                            <li class="menu-item"><a href="https://1.envato.market/EA4JP" target="_blank"
                                                     class="menu-link px-2">Purchase</a></li>
                        </ul>
                        <!--end::Menu-->
                    </div>
                    <!--end::Footer-->
                </div>
                <!--end:::Main-->


            </div>
            <!--end::Wrapper container-->
        </div>
        <!--end::Wrapper-->


    </div>
    <!--end::Page-->
</div>
<!--end::App-->


<!--begin::Scrolltop-->
<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
    <i class="ki-duotone ki-arrow-up"><span class="path1"></span><span class="path2"></span></i></div>
<!--end::Scrolltop-->


<!--begin::Javascript-->
<script>
    var hostUrl = "/assets_demo46/";
</script>

<!--begin::Global Javascript Bundle(mandatory for all pages)-->
<script src="/assets_demo46/plugins/global/plugins.bundle.js"></script>
<script src="/assets_demo46/js/scripts.bundle.js"></script>
<!--end::Global Javascript Bundle-->

<!--begin::Vendors Javascript(used for this page only)-->
<!--end::Vendors Javascript-->

<!--begin::Custom Javascript(used for this page only)-->
@stack('customScript')
<!--end::Custom Javascript-->
<!--end::Javascript-->

</body>
<!--end::Body-->
</html>
