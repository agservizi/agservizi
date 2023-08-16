<!--begin::Header-->
<div id="kt_app_header" class="app-header ">
    <!--begin::Header container-->
    <div class="app-container  container-xxl d-flex align-items-stretch justify-content-between " id="kt_app_header_container">
        <!--begin::Logo-->
        <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0 me-lg-15">
            <a href="?page=index">
                <img alt="Logo" src="/assets_backend/media/logos/default.svg" class="h-20px h-lg-30px app-sidebar-logo-default theme-light-show"/>
                <img alt="Logo" src="/assets_backend/media/logos/default-dark.svg" class="h-20px h-lg-30px app-sidebar-logo-default theme-dark-show"/>
            </a>
        </div>
        <!--end::Logo-->
        <!--begin::Header wrapper-->
        <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1" id="kt_app_header_wrapper">
            @include('metronic.layout.partials.header._menu._menu')
            @include('metronic.layout.partials.header._navbar')
        </div>
        <!--end::Header wrapper-->
    </div>
    <!--end::Header container-->
</div>
<!--end::Header-->
