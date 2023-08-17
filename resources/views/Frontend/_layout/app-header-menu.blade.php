<div
    class="app-header-menu app-header-mobile-drawer align-items-stretch"

    data-kt-drawer="true"
    data-kt-drawer-name="app-header-menu"
    data-kt-drawer-activate="{default: true, lg: false}"
    data-kt-drawer-overlay="true"
    data-kt-drawer-width="250px"
    data-kt-drawer-direction="start"
    data-kt-drawer-toggle="#kt_app_header_menu_toggle"

    data-kt-swapper="true"
    data-kt-swapper-mode="{default: 'append', lg: 'prepend'}"
    data-kt-swapper-parent="{default: '#kt_app_body', lg: '#kt_app_header_wrapper'}"
>
    <!--begin::Menu-->
    <div
        class=" menu
            menu-rounded
            menu-active-bg
            menu-state-primary
            menu-column
            menu-lg-row
            menu-title-gray-700
            menu-icon-gray-500
            menu-arrow-gray-500
            menu-bullet-gray-500
            my-5 my-lg-0 align-items-stretch fw-semibold px-2 px-lg-0
        "
        id="kt_app_header_menu"
        data-kt-menu="true"
    >
        <!--begin:Menu item-->
        <div
            class="menu-item menu-here-bg me-0 me-lg-2">
            <!--begin:Menu link-->
            <a  class="menu-link" href="{{action([\App\Http\Controllers\Frontend\SpedizioneController::class,'index'])}}"><span class="menu-title">Controlla la tua spedizione</span></a><!--end:Menu link-->
            <!--begin:Menu sub-->

            <!--end:Menu sub-->
        </div><!--end:Menu item-->

    </div>
    <!--end::Menu-->
</div>
