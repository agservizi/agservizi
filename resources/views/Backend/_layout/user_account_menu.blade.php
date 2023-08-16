<div class="cursor-pointer symbol symbol-30px symbol-md-40px"
     data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
     data-kt-menu-attach="parent"
     data-kt-menu-placement="bottom-end">
    @if(\App\Http\HelperForMetronic::NOME_COMPLETO_IN_TOPBAR)
        <div class="">
            <span class="badge bg-success text-inverse-success fw-bolder">{{\Illuminate\Support\Facades\Auth::user()->nominativo()}}</span>
        </div>
    @else
        <div class="symbol symbol-35px symbol-circle">
            <img src="/images/avatar.png">
        </div>
    @endif
</div>
<!--begin::User account menu-->
<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px" data-kt-menu="true">
    <!--begin::Menu item-->
    <div class="menu-item px-3">
        <div class="menu-content d-flex align-items-center px-3">
            @if(\App\Http\HelperForMetronic::NOME_COMPLETO_IN_TOPBAR)
                <div class="">
                    <span class="badge bg-success text-inverse-success fw-bolder">{{\Illuminate\Support\Facades\Auth::user()->nominativo()}}</span>
                </div>
            @else
                <div class="symbol symbol-35px symbol-circle">
                    <span class="symbol-label bg-{{Auth::user()->coloreRuolo()}} text-inverse-success fw-bolder">{{\Illuminate\Support\Facades\Auth::user()->iniziali()}}</span>
                </div>
            @endif
        </div>
    </div>
    <!--end::Menu item-->
    <div class="separator my-2"></div>
    <div class="menu-item px-5">
        <a href="{{action([\App\Http\Controllers\Backend\DatiUtenteController::class, 'show'])}}" class="menu-link px-5">I tuoi dati</a>
    </div>
    <div class="separator my-2"></div>
    <div class="menu-item px-5">
        <a href="/logout" class="menu-link px-5">Esci</a>
    </div>
    <!--end::Menu item-->
    @if(env('APP_ENV')=='local')
        <div class="separator my-2"></div>
        <div class="menu-item px-5">
            <a href="https://preview.keenthemes.com/metronic8/demo1/documentation/getting-started.html" class="menu-link px-5" target="_blank">Documentazione Metronic</a>
        </div>
        @if(config('configurazione.url_online'))
            <div class="menu-item px-5">
                <a href="{{config('configurazione.url_online')}}" class="menu-link px-5" target="_blank">Sito produzione</a>
            </div>
        @endif
    @endif

</div>
<!--end::User account menu-->
