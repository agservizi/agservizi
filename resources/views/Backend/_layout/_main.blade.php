<!DOCTYPE html>
<html lang="it">
<!--8.1.8-->
<head>
    <base href=""/>
    <meta charset="utf-8"/>
    <title>{{$titoloPagina??config('configurazione.tag_title')}}</title>
    <meta name="description" content=""/>
    <meta name="keywords" content=""/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="_token" content="{{csrf_token()}}">

    <!--begin::favicon-->
    <link rel="shortcut icon" href="/loghi/favicon.ico"/>
    <link rel="apple-touch-icon" sizes="180x180" href="/loghi/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/loghi/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/loghi/favicon-16x16.png">
    <link rel="manifest" href="/loghi/site.webmanifest">
    <link rel="mask-icon" href="/loghi/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700"/>
    <!--end::Fonts-->
    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="/assets_backend/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css"/>
    <link href="/assets_backend/css/style.bundle.css" rel="stylesheet" type="text/css"/>
    <link href="/assets_backend/css-miei/mio.css" rel="stylesheet" type="text/css"/>
    <!--end::Global Stylesheets Bundle-->
    @stack('customCss')
</head>
<!--end::Head-->
<!--begin::Body-->
<body id="kt_app_body" data-kt-app-layout="light-header" data-kt-app-header-fixed="true" data-kt-app-toolbar-enabled="true" class="app-default" data-kt-app-toolbar-fixed="true">


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
            <div class="app-container  container-xxl d-flex align-items-stretch justify-content-between "
                 id="kt_app_header_container">
                <!--begin::Logo-->
                <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0 me-lg-5">
                    <a href="/">
                        <img alt="Logo" src="/loghi/logo-piccolo.png"
                             class="h-20px h-lg-30px app-sidebar-logo-default theme-light-show"/>
                        <img alt="Logo" src="/loghi/logo-piccolo.png"
                             class="h-20px h-lg-30px app-sidebar-logo-default theme-dark-show"/>
                    </a>
                </div>
                <!--end::Logo-->
                <!--begin::Header wrapper-->
                <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1"
                     id="kt_app_header_wrapper">
                    <!--begin::Menu wrapper-->
                    <div
                        class="app-header-menu  app-header-mobile-drawer align-items-stretch"
                        data-kt-drawer="true"
                        data-kt-drawer-name="app-header-menu"
                        data-kt-drawer-activate="{default: true, lg: false}"
                        data-kt-drawer-overlay="true"
                        data-kt-drawer-width="250px"
                        data-kt-drawer-direction="end"
                        data-kt-drawer-toggle="#kt_app_header_menu_toggle"
                        data-kt-swapper="true"
                        data-kt-swapper-mode="{default: 'append', lg: 'prepend'}"
                        data-kt-swapper-parent="{default: '#kt_app_body', lg: '#kt_app_header_wrapper'}"
                    >
                        <!--begin::Menu-->
                        @include('Backend._layout.app-header-menu')
                        <!--end::Menu-->
                    </div>
                    <!--end::Menu wrapper-->                    <!--begin::Navbar-->
                    <div class="app-navbar flex-shrink-0">
                        <!--begin::Search-->
                        <!--end::Search-->
                        <!--begin::Theme mode-->
                        <div class="app-navbar-item ms-1 ms-md-3">
                            <!--begin::Menu toggle-->
                            <a href="#"
                               class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-30px h-30px w-md-40px h-md-40px"
                               data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-attach="parent"
                               data-kt-menu-placement="bottom-end">
                                <i class="ki-duotone ki-night-day theme-light-show fs-2 fs-lg-1"><span
                                        class="path1"></span><span class="path2"></span><span
                                        class="path3"></span><span class="path4"></span><span class="path5"></span><span
                                        class="path6"></span><span class="path7"></span><span
                                        class="path8"></span><span class="path9"></span><span class="path10"></span></i>
                                <i
                                    class="ki-duotone ki-moon theme-dark-show fs-2 fs-lg-1"><span
                                        class="path1"></span><span class="path2"></span></i></a>
                            <!--begin::Menu toggle-->
                            <!--begin::Menu-->
                            <div
                                class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px"
                                data-kt-menu="true" data-kt-element="theme-mode-menu">
                                <!--begin::Menu item-->
                                <div class="menu-item px-3 my-0">
                                    <a href="#" class="menu-link px-3 py-2" data-kt-element="mode"
                                       data-kt-value="light">
            <span class="menu-icon" data-kt-element="icon">
                <i class="ki-duotone ki-night-day fs-2"><span class="path1"></span><span class="path2"></span><span
                        class="path3"></span><span class="path4"></span><span
                        class="path5"></span><span class="path6"></span><span class="path7"></span><span
                        class="path8"></span><span class="path9"></span><span
                        class="path10"></span></i>            </span>
                                        <span class="menu-title">
                Light
            </span>
                                    </a>
                                </div>
                                <!--end::Menu item-->
                                <!--begin::Menu item-->
                                <div class="menu-item px-3 my-0">
                                    <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="dark">
            <span class="menu-icon" data-kt-element="icon">
                <i class="ki-duotone ki-moon fs-2"><span class="path1"></span><span class="path2"></span></i>            </span>
                                        <span class="menu-title">
                Dark
            </span>
                                    </a>
                                </div>
                                <!--end::Menu item-->
                                <!--begin::Menu item-->
                                <div class="menu-item px-3 my-0">
                                    <a href="#" class="menu-link px-3 py-2" data-kt-element="mode"
                                       data-kt-value="system">
            <span class="menu-icon" data-kt-element="icon">
                <i class="ki-duotone ki-screen fs-2"><span class="path1"></span><span class="path2"></span><span
                        class="path3"></span><span
                        class="path4"></span></i>            </span>
                                        <span class="menu-title">
                System
            </span>
                                    </a>
                                </div>
                                <!--end::Menu item-->
                            </div>
                            <!--end::Menu-->
                        </div>
                        <!--end::Theme mode-->
                        @if(\App\Http\HelperForMetronic::PULSANTE_FRONTEND)
                            <div class="d-flex align-items-center ms-1 ms-lg-3">
                                <a href="/" class="btn  btn-active-light btn-active-color-primary ">
                                    Frontend
                                </a>
                            </div>
                        @endif
                        <!--begin::User menu-->
                        <div class="app-navbar-item ms-1 ms-md-3" id="kt_header_user_menu_toggle">
                            <!--begin::Menu wrapper-->
                            @include('Backend._layout.user_account_menu')
                            <!--end::Menu wrapper-->
                        </div>
                        <!--end::User menu-->
                        <!--begin::Header menu toggle-->
                        <div class="app-navbar-item d-lg-none ms-2 me-n2" title="Show header menu">
                            <div class="btn btn-flex btn-icon btn-active-color-primary w-30px h-30px"
                                 id="kt_app_header_menu_toggle">
                                <i class="ki-duotone ki-element-4 fs-1"><span class="path1"></span><span
                                        class="path2"></span></i></div>
                        </div>
                        <!--end::Header menu toggle-->
                    </div>
                    <!--end::Navbar-->
                </div>
                <!--end::Header wrapper-->
            </div>
            <!--end::Header container-->
        </div>
        <!--end::Header-->        <!--begin::Wrapper-->
        <div class="app-wrapper  flex-column flex-row-fluid " id="kt_app_wrapper">
            <!--begin::Main-->
            <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                <!--begin::Content wrapper-->
                <div class="d-flex flex-column flex-column-fluid">
                    <!--begin::Toolbar-->
                    <div id="kt_app_toolbar" class="app-toolbar  py-3 py-lg-6 "
                    >
                        <!--begin::Toolbar container-->
                        <div id="kt_app_toolbar_container" class="app-container  container-xxl d-flex flex-stack ">
                            <!--begin::Page title-->
                            <div class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                                <!--begin::Title-->
                                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                                    {{$titoloPagina??''}}
                                </h1>
                                <!--end::Title-->
                                <!--begin::Breadcrumb-->
                                @includeWhen(isset($breadcrumbs),'Backend._layout.breadcrumbs')
                                <!--end::Breadcrumb-->
                            </div>
                            <!--end::Page title-->
                            <!--begin::Actions-->
                            <div class="d-flex align-items-center gap-2 gap-lg-3">
                                @yield('toolbar')
                            </div>
                            <!--end::Actions-->
                        </div>
                        <!--end::Toolbar container-->
                    </div>
                    <!--end::Toolbar-->
                    <!--begin::Content-->
                    <div id="kt_app_content" class="app-content  flex-column-fluid ">
                        <!--begin::Content container-->
                        <div id="kt_app_content_container" class="app-container  container-xxl ">
                            @yield('content')
                        </div>
                        <!--end::Content container-->
                    </div>
                    <!--end::Content-->
                </div>
                <!--end::Content wrapper-->
                <!--begin::Footer-->
                <div id="kt_app_footer" class="app-footer ">
                    <!--begin::Footer container-->
                    <div
                        class="app-container  container-xxl d-flex flex-column flex-md-row flex-center flex-md-stack py-3 ">
                        <!--begin::Copyright-->
                        <div class="text-dark order-2 order-md-1">
                            <span class="text-muted fw-semibold me-1">2023&copy;</span>
                            <span class="text-gray-800 text-hover-primary">{{config('configurazione.tag_title')}}</span>
                        </div>
                        <!--end::Copyright-->
                        <!--begin::Menu-->
                        <ul class="menu menu-gray-600 menu-hover-primary fw-semibold order-1">
                            @if(false)
                                <li class="menu-item"><a href="https://keenthemes.com" target="_blank"
                                                         class="menu-link px-2">About</a></li>
                                <li class="menu-item"><a href="https://devs.keenthemes.com" target="_blank"
                                                         class="menu-link px-2">Support</a></li>
                                <li class="menu-item"><a href="https://1.envato.market/EA4JP" target="_blank"
                                                         class="menu-link px-2">Purchase</a></li>
                            @endif
                        </ul>
                        <!--end::Menu-->
                    </div>
                    <!--end::Footer container-->
                </div>
                <!--end::Footer-->            </div>
            <!--end:::Main-->
        </div>
        <!--end::Wrapper-->
    </div>
    <!--end::Page-->
</div>
<!--end::App-->
@include('Backend._layout.modal')
<!--begin::Scrolltop-->
<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
    <i class="ki-duotone ki-arrow-up"><span class="path1"></span><span class="path2"></span></i>
</div>
<!--end::Scrolltop--><!--begin::Javascript-->
<script>
    var hostUrl = "/assets_backend/";
</script>
<!--begin::Global Javascript Bundle(mandatory for all pages)-->
<script src="/assets_backend/plugins/global/plugins.bundle.js"></script>
<script src="/assets_backend/js/scripts.bundle.js"></script>
<script src="/assets_backend/js-miei/mieiScript.js?v=5"></script>

<!--end::Global Javascript Bundle-->
@stack('customScript')
<!--end::Javascript-->
<script>
    $(function () {
        modalAjax();
        $('.menu-click').click(function () {
            location.href = $(this).attr('href');
        });
    });
</script>
</body>
<!--end::Body-->
</html>
