<div class="table-responsive">
    <table class="table table-row-bordered" id="tabella-elenco">
        <thead>
        <tr class="fw-bolder fs-6 text-gray-800">
            <th>Nominativo</th>
            <th>Contatti</th>
            <th class="d-none d-md-table-cell">Ultimo accesso</th>
            <th class="d-none d-lg-table-cell text-center">Ruolo</th>
            <th class=""></th>
        </tr>
        </thead>
        <tbody id="t-body">
        @foreach($records as $record)
            <tr>
                <td>{{$record->nominativo()}}</td>
                <td>{!! $record->contatti() !!}</td>
                <td class="d-none d-md-table-cell">{{$record->ultimo_accesso?$record->ultimo_accesso->format('d/m/Y H:i'):''}}</td>
                <td class="d-none d-lg-table-cell  text-center">
                    {!! $record->badgeRuolo() !!}
                </td>
                <td class="text-end">
                    <div class="btn-group" role="group">
                        <a class="btn btn-sm btn-light btn-active-light-success  mx-2"
                           href="{{action([\App\Http\Controllers\Backend\OperatoreController::class,'show'],$record->id)}}">Vedi</a>
                        <a
                                class="btn btn-sm btn-light btn-active-light-primary"
                                href="{{action([\App\Http\Controllers\Backend\OperatoreController::class,'edit'],$record->id)}}">Modifica</a>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<div class="w-100 text-center">
    {{$records->links()}}
</div>
