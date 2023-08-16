<div class="table-responsive">
    <table class="table table-row-bordered" id="tabella-elenco">
        <thead>
        <tr class="fw-bolder fs-6 text-gray-800">
            <th class="">Corriere</th>
            <th class="">Descrizione</th>
            <th class="text-center">Abilitato</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($records as $record)
            <tr class="">
                <td class="">
                    @if($record->corriere_id)
                        {{$record->corriere_id}}
                    @endif
                </td>
                <td class="">{{$record->descrizione}}</td>
                <td class="text-center">
                    @if($record->abilitato)
                        <i class="fas fa-check fs-3" style="color: #26C281;"></i>
                    @endif
                </td>

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
