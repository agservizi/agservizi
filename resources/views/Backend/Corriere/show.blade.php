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
                <a href="{{action([\App\Http\Controllers\Backend\ServizioController::class,'create'],['corriere_id'=>$record->id])}}"
                   class="menu-link px-3">Nuovo {{ucfirst(\App\Models\Servizio::NOME_SINGOLARE)}}</a>
            </div>

        </div>
        <!--end::Menu 3-->
    </div>
@endsection
@section('content')
    @include('Backend._components.alertMessage')
    <div class="card mb-5 mb-xl-10">
        <div class="card-body pt-9 pb-0">
            <!--begin::Details-->
            <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
                <!--begin: Pic-->
                <!--end::Pic-->
                <!--begin::Info-->
                <div class="flex-grow-1">
                    <!--begin::Title-->
                    <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                        <!--begin::User-->
                        <div class="d-flex flex-column">
                            <!--begin::Name-->
                            <div class="d-flex align-items-center mb-2">
                                <span class="text-gray-900 fs-2 fw-bold me-1">{{$record->denominazione}}</span>
                            </div>
                            <!--end::Name-->
                        </div>
                        <!--end::User-->
                    </div>
                    <!--end::Title-->

                    <!--begin::Stats-->
                    <div class="d-flex flex-wrap flex-stack">

                    </div>
                    <!--end::Stats-->
                </div>
                <!--end::Info-->
            </div>
            <!--end::Details-->
        </div>
    </div>


    <div class="card">
        <div class="card-header">
            <div class="card-title flex-column">

                Servizi
            </div>
            <div class="card-toolbar">

            </div>
        </div>
        <div class="card-body p-9 pt-5 mb-0">
            <div class="table-responsive">
                <table class="table table-row-bordered" id="tabella-elenco">
                    <thead>
                    <tr class="fw-bolder fs-6 text-gray-800">
                        <th class="">Descrizione</th>
                        <th class="text-center">Abilitato</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($records as $record)
                        <tr class="">
                            <td class="">{{$record->descrizione}}</td>
                            <td class="text-center">
                                @if($record->abilitato)
                                    <i class="fas fa-check fs-3" style="color: #26C281;"></i>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <a data-targetZ="kt_modal" data-toggleZ="modal-ajax"
                                   class="btn btn-sm btn-light btn-active-light-primary"
                                   href="{{action([\App\Http\Controllers\Backend\ServizioController::class,'edit'],$record->id)}}">Modifica</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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
