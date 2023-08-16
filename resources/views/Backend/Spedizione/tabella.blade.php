<div class="table-responsive">
    <table class="table table-row-bordered" id="tabella-elenco">
        <thead>
        <tr class="fw-bolder fs-6 text-gray-800">
            <th class="">Corriere</th>
            <th class="">Servizio</th>
            <th class="">Cliente</th>
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
                <td class="">
                    {{$record->cliente->nominativo()}}
                </td>
                <td class="">{{$record->data_spedizione?->format("d/m/Y")}}</td>
                <td class="">{{$record->stato_spedizione}}</td>
                <td class="">{{$record->denominazione_destinatario}}</td>
                <td class="">{!! $record->tracking($record->corriere->url_tracking) !!}</td>
                <td class="text-end text-nowrap">
                    <a data-targetZ="kt_modal" data-toggleZ="modal-ajax"
                       class="btn btn-sm btn-light btn-active-light-primary"
                       href="{{action([$controller,'edit'],$record->id)}}">Modifica</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@if($records instanceof \Illuminate\Pagination\LengthAwarePaginator )
    <div class="w-100 text-center">
        {{$records->links()}}
    </div>
@endif
