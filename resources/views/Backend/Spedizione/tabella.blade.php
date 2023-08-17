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
        @include('Backend.Spedizione.rigaTabella')
        </tbody>
    </table>
</div>
@if($records instanceof \Illuminate\Pagination\LengthAwarePaginator )
    <div class="w-100 text-center">
        {{$records->links()}}
    </div>
@endif
