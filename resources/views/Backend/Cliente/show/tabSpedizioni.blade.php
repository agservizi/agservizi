<table class="table table-row-bordered">
    <thead>
    <tr class="fw-bolder fs-6 text-gray-800">
        <th class="">Descrizione</th>
        <th class="text-end">Anno</th>
        <th class="">Data upload</th>
        <th class="text-end">Dimensione</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    @foreach($records as $record)
        <tr class="">
            <td class="">
                {{$record->descrizione_allegato}}
            </td>
            <td class="text-end">
                {{$record->anno}}
            </td>
            <td class="">
                {{$record->created_at->format("d/m/Y")}}
            </td>
            <td class="text-end">
                {{\App\humanFileSize($record->dimensione_file)}}
            </td>
            <td class="text-end text-nowrap">

                <a data-targetZ="kt_modal" data-toggleZ="modal-ajax"
                   class="btn btn-sm btn-light"
                   href="{{action([\App\Http\Controllers\Backend\AllegatoCondominioController::class,'download'],['condominioId'=>$condominioId,$record->id])}}">Download</a>
                <a data-targetZ="kt_modal" data-toggleZ="modal-ajax"
                   class="btn btn-sm btn-light btn-active-light-primary"
                   href="{{action([\App\Http\Controllers\Backend\AllegatoCondominioController::class,'edit'],['condominioId'=>$condominioId,$record->id])}}">Modifica</a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
