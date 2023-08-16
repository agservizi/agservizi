<div class="table-responsive">
    <table class="table align-middle table-row-dashed gy-5 fs-6" id="kt_table_customers_payment">
        <thead class="border-bottom border-gray-200  fw-bolder">
        <tr class="text-start gs-0">
            <th class="">Denominazione</th>
            <th class="">Città</th>
            <th>Contatti</th>
            <th class="text-end min-w-70px pe-4"></th>
        </tr>
        </thead>
        <tbody class="fw-bold text-gray-700">
        @foreach($records as $record)
            <tr>
                <td>
                    {{$record->denominazione()}}
                </td>
                <td>
                    @if($record->nazione=='IT')
                        {{$record->comune->comuneConTarga()}}
                    @else
                        {{$record->citta}}
                    @endif
                </td>
                <td>{!! $record->contatti() !!}</td>

                <td class="pe-0 text-end" style="white-space: nowrap;">
                    <a class="btn btn-sm btn-light btn-active-light-success" href="{{action([\App\Http\Controllers\Backend\ClienteController::class,'show'],$record->id)}}">Vedi</a>
                    @if(false)
                        <a class="btn btn-sm btn-light btn-active-light-primary"
                           href="{{action([\App\Http\Controllers\Backend\ClienteController::class,'edit'],$record->id)}}">Modifica</a>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
{{$records->links()}}
