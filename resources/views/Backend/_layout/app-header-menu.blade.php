<div class="menu menu-rounded menu-column menu-lg-row my-5 my-lg-0 align-items-stretch fw-semibold px-2 px-lg-0"
     id="kt_app_header_menu"
     data-kt-menu="true"
>

    <!--begin:Menu item-->
    <a href="{{action([\App\Http\Controllers\Backend\SpedizioneController::class,'index'])}}"
       class="menu-item me-0 me-lg-2">
        <span class="menu-link"><span
                class="menu-title">{{ucfirst(\App\Models\Spedizione::NOME_PLURALE)}}</span></span>
    </a>
    <!--end:Menu item-->
    <!--begin:Menu item-->
    <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start"
         class="menu-item menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2">
        <!--begin:Menu link-->
        <span class="menu-link">
                <span class="menu-title">Gestione</span>
                <span class="menu-arrow d-lg-none"></span>
            </span>
        <!--end:Menu link-->
        <!--begin:Menu sub-->
        <div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown px-lg-2 py-lg-4 w-lg-250px">
            <!--begin:Menu item-->
            <div class="menu-item">
                <div class="menu-item">
                    <a class="menu-link"
                       href="{{action([\App\Http\Controllers\Backend\CorriereController::class,'index'])}}">
                        <span class="menu-title">Corrieri</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link"
                       href="{{action([\App\Http\Controllers\Backend\StatoSpedizioneController::class,'index'])}}">
                        <span class="menu-title">Stati spedizioni</span>
                    </a>
                </div>
            </div>
            <!--end:Menu item-->
        </div>
        <!--end:Menu sub-->
    </div>
    <!--end:Menu item-->
    <!--begin:Menu item-->
    <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start"
         class="menu-item menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2">
        <!--begin:Menu link-->
        <span class="menu-link">
                <span class="menu-title">Registri</span>
                <span class="menu-arrow d-lg-none"></span>
            </span>
        <!--end:Menu link-->
        <!--begin:Menu sub-->
        <div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown px-lg-2 py-lg-4 w-lg-250px">
            <!--begin:Menu item-->
            <div class="menu-item">
                <div class="menu-item">
                    <a class="menu-link"
                       href="{{action([\App\Http\Controllers\Backend\RegistriController::class,'index'],'login')}}">
                        <span class="menu-title">Login</span>
                    </a>
                </div>
                @if(\Illuminate\Support\Facades\Auth::id()==1)
                    <div class="menu-item">
                        <a class="menu-link"
                           href="{{action([\App\Http\Controllers\Backend\RegistriController::class,'index'],'backup-db')}}">
                            <span class="menu-title">Backup DB</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link" href="/backend/log-viewer/logs" target="_blank">
                            <span class="menu-title">Log viewer</span>
                        </a>
                    </div>
                @endif

            </div>
            <!--end:Menu item-->
        </div>
        <!--end:Menu sub-->
    </div>
    <!--end:Menu item-->

</div>
