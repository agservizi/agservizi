@foreach($records as $record)
    <tr id="tr_{{$record->id}}">
        <td class="">
            {{$record->corriere->denominazione}}
        </td>
        <td class="">{{$record->servizio->descrizione}}</td>
        <td class="">
            {{$record->cliente->nominativo()}}
        </td>
        <td class="">{{$record->data_spedizione?->format("d/m/Y")}}</td>
        <td class="">
            <a href="{{action([$controller,'modalCambiaStato'],$record->id)}}" data-target="kt_modal"
               data-toggle="modal-ajax"
               id="stato_{{$record->id}}">{!! $record->statoSpedizione->badgeStato() !!}</a>
            @if($record->letteraDiVettura)
                <a href="{{action([\App\Http\Controllers\Backend\SpedizioneController::class,'downloadAllegato'],$record->letteraDiVettura->id)}}">
                    <i class="fa-solid fa-file" data-bs-toggle="tooltip" title="Lettera di vettura"></i>
                </a>
            @endif
            @if($record->pod)
                <a href="{{action([\App\Http\Controllers\Backend\SpedizioneController::class,'downloadAllegato'],$record->pod->id)}}">
                    <i class="fa-solid fa-file-circle-check" data-bs-toggle="tooltip" title="Pod"></i>
                </a>
            @endif
        </td>
        <td class="">{{$record->denominazione_destinatario}}</td>
        <td class="">{!! $record->tracking($record->corriere->url_tracking) !!}</td>
        <td class="text-end text-nowrap">
            <a data-targetZ="kt_modal" data-toggleZ="modal-ajax"
               class="btn btn-sm btn-light btn-active-light-primary"
               href="{{action([$controller,'edit'],$record->id)}}">Modifica</a>
        </td>
    </tr>
@endforeach
