@extends('Frontend._layout._main')
@section('content')
    <div class="card card-flush">
        <!--begin::Card header-->
        <div class="card-header align-items-center py-5 gap-2 gap-md-5">
            <!--begin::Card title-->
            <div class="card-title">
                Elenco spedizioni
            </div>
            <!--end::Card title-->
            <!--begin::Card toolbar-->
            <div class="card-toolbar flex-row-fluid justify-content-end gap-5">

            </div>
            <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-bordered" id="tabella-elenco">
                    <thead>
                    <tr class="fw-bolder fs-6 text-gray-800">
                        <th class="">Corriere</th>
                        <th class="">Servizio</th>

                        <th class="">Data spedizione</th>
                        <th class="">Stato spedizione</th>
                        <th class="">Denominazione destinatario</th>
                        <th class="">Codice tracking</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($records as $record)
                        <tr class="">
                            <td class="">
                                {{$record->corriere->denominazione}}
                            </td>
                            <td class="">{{$record->servizio->descrizione}}</td>

                            <td class="">{{$record->data_spedizione?->format("d/m/Y")}}</td>
                            <td class="">{!! $record->statoSpedizione->badgeStato() !!}</td>
                            <td class="">{{$record->denominazione_destinatario}}</td>
                            <td class="">{!! $record->tracking($record->corriere->url_tracking) !!}</td>
                            <td class="text-end text-nowrap">
                                @if(false)
                                    <a data-targetZ="kt_modal" data-toggleZ="modal-ajax"
                                       class="btn btn-sm btn-light btn-active-light-primary"
                                       href="{{action([$controller,'edit'],$record->id)}}">Modifica</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="w-100 text-center">
                {{$records->links()}}
            </div>
        </div>
        <!--end::Card body-->
    </div>
@endsection
