@extends('Backend._layout._main')
@section('toolbar')

    <div class="me-0">
        <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary" data-kt-menu-trigger="click"
                data-kt-menu-placement="bottom-end">
            <i class="bi bi-three-dots fs-3"></i>
        </button>
        <!--begin::Menu 3-->
        <div
            class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3"
            data-kt-menu="true">
            <!--begin::Heading-->
            <div class="menu-item px-3">
                <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">Azioni</div>
            </div>
            <!--end::Heading-->
            <div class="menu-item px-3">
                <a data-targetZ="kt_modal" data-toggleZ="modal-ajax"
                   class="menu-link px-3"
                   href="{{action([$controller,'edit'],$record->id)}}">Modifica</a>
            </div>

            <div class="separator mt-3 mb-3 opacity-75"></div>
            <div class="menu-item px-3">
                <a href="{{action([$controller,'azioni'],['id'=>$record->id,'azione'=>'impersona'])}}"
                   class="menu-link px-3 azione">Impersona</a>
            </div>
            @if(false)
                <div class="menu-item px-3">
                    <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">Condomini</div>
                </div>

                <div class="menu-item px-3">
                    <a data-target="kt_modal" data-toggle="modal-ajax"
                       class="menu-link px-3"
                       href="{{action([\App\Http\Controllers\Backend\ModalController::class,'show'],['modal'=>'importa-condomini','id'=>$record->id])}}">
                        Importa da Excel</a>
                </div>
                <div class="separator mt-3 opacity-75"></div>
                <div class="menu-item px-3">
                    <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">Allegati</div>
                </div>
                <div class="menu-item px-3">
                    <a href="{{action([\App\Http\Controllers\Backend\AllegatoCondominioController::class,'create'],['condominioId'=>$record->id])}}"
                       class="menu-link px-3">Carica allegato</a>
                </div>
            @endif
        </div>
        <!--end::Menu 3-->
    </div>
@endsection
@section('content')
    @include('Backend._components.alertMessage')
    @include('Backend.Cliente.show.datiCliente')
    @if(false)
        <div class="card">
            <div class="card-header">
                <div class="card-title flex-column">
                    <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-6">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#Allegati" data-toggle="tabajax"
                               data-url="{{action([$controller,'tab'],[$record->id,'tab_allegati_condominio'])}}">Spedizioni</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_condomini" data-toggle="tabajax"
                               data-url="{{action([$controller,'tab'],[$record->id,'tab_condomini_condominio'])}}">{{ucfirst(\App\Models\Condomino::NOME_PLURALE)}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_condomini_cessati" data-toggle="tabajax"
                               data-url="{{action([$controller,'tab'],[$record->id,'tab_condomini_cessati_condominio'])}}">{{ucfirst(\App\Models\Condomino::NOME_PLURALE)}}
                                cessati</a>
                        </li>
                    </ul>
                </div>
                <div class="card-toolbar">

                </div>
            </div>
            <div class="card-body p-9 pt-5 mb-0">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="Allegati" role="tabpanel">
                        @include('Backend.Condominio.show.tabAllegatiCondominio',['condominioId'=>$record->id])
                    </div>
                    <div class="tab-pane fade" id="tab_condomini" role="tabpanel">
                    </div>
                    <div class="tab-pane fade" id="tab_condomini_cessati" role="tabpanel">
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
@push('customCss')
@endpush
@push('customScript')
    <script>
        $(function () {
            azioneAjax();
            tabAjax();
            $(document).on('click', 'a.page-link', function (e) {
                e.preventDefault();
                var url = $(this).attr('href');
                var tab = $(this).closest('.tab-pane').attr('id');
                paginazioneAjax(url, '#' + tab);
            });
        });
    </script>
@endpush
