<div class="table-responsive">
    <table class="table table-row-bordered" id="tabella-elenco">
        <thead>
        <tr class="fw-bolder fs-6 text-gray-800">
            <th class="">Denominazione</th>
            <th class="">Logo</th>
            <th class="">Url tracking</th>
            <th class="text-center">Abilitato</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($records as $record)
            <tr class="">
                <td class="">{{$record->denominazione}}</td>
                <td class="">
                    @if($record->logo)
                        <div class="symbol symbol-50px symbol-2by3 me-3">
                            <img src="{{$record->immagineLogo()}}" class="" alt="">
                        </div>
                    @endif
                </td>
                <td class="">{{$record->url_tracking}}</td>
                <td class="text-center">
                    @if($record->abilitato)
                        <i class="fas fa-check fs-3" style="color: #26C281;"></i>
                    @endif
                </td>
                <td class="text-end text-nowrap">
                    <a data-targetZ="kt_modal" data-toggleZ="modal-ajax"
                       class="btn btn-sm btn-light btn-active-light-success"
                       href="{{action([$controller,'show'],$record->id)}}">Vedi</a>
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
