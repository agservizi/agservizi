@extends('Backend._layout._main')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <!--begin::Table-->
                <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4">
                    <!--begin::Table head-->
                    <thead>
                    <tr class="border-0">
                        <th class="p-0 w-80px"></th>
                        <th class="p-0 min-w-150px"></th>
                        <th class="p-0 min-w-150px"></th>
                        <th class="p-0 min-w-100px"></th>
                        <th class="p-0 min-w-100px"></th>
                    </tr>
                    </thead>
                    <!--end::Table head-->
                    <!--begin::Table body-->
                    <tbody>
                    @foreach($records as $record)
                        <tr>
                            <td>
                                <div class="symbol symbol-75px me-2" style="border: 1px solid #B5B5C3;">
                                    <span class="symbol-label">
                                        <img class="img-thumbnail intense"
                                             src="{{Storage::url($record->nomeFileConPath())}}" alt="">
                                    </span>
                                </div>
                            </td>
                            <td class="text-muted fw-bold">
                                <div  class="text-dark fw-bolder mb-1 fs-6">{{$record->titolo}}</div>
                                <div class="mb-2">{{$record->testo}}</div>
                                url: <a href="{{$record->url}}" target="_blank"> {{$record->url}}</a>
                            </td>
                            <td>

                                <span class="text-muted fw-bold d-block">{{$record->utente->nominativo()}}</span>
                                <span class="text-muted fw-bold d-block">{{$record->created_at->format('d/m/Y')}}</span>
                            </td>

                            <td class="text-end" id="label_{{$record->id}}">
                                {!! \App\Http\HelperForMetronic::labelSegnalazione($record->risolto) !!}
                            </td>
                            <td class="pe-0 text-end">
                                <a href="#" class="btn btn-sm btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end"
                                   data-kt-menu-flip="top-end">Azione
                                    <!--begin::Svg Icon | path: icons/duotone/Navigation/Angle-down.svg-->
                                    <span class="svg-icon svg-icon-5 m-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                             height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                                <path
                                                    d="M6.70710678,15.7071068 C6.31658249,16.0976311 5.68341751,16.0976311 5.29289322,15.7071068 C4.90236893,15.3165825 4.90236893,14.6834175 5.29289322,14.2928932 L11.2928932,8.29289322 C11.6714722,7.91431428 12.2810586,7.90106866 12.6757246,8.26284586 L18.6757246,13.7628459 C19.0828436,14.1360383 19.1103465,14.7686056 18.7371541,15.1757246 C18.3639617,15.5828436 17.7313944,15.6103465 17.3242754,15.2371541 L12.0300757,10.3841378 L6.70710678,15.7071068 Z"
                                                    fill="#000000" fill-rule="nonzero"
                                                    transform="translate(12.000003, 11.999999) rotate(-180.000000) translate(-12.000003, -11.999999)"></path>
                                            </g>
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                </a>
                                <!--begin::Menu-->
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4"
                                     data-kt-menu="true" style="">
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a href="{{action([$controller,'update'],['id'=>$record->id,'risolto'=>25])}}" class="menu-link px-3 update">{!! \App\Http\HelperForMetronic::labelSegnalazione(25) !!}</a>

                                    </div>
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a href="{{action([$controller,'update'],['id'=>$record->id,'risolto'=>50])}}" class="menu-link px-3 update">{!! \App\Http\HelperForMetronic::labelSegnalazione(50) !!}</a>
                                    </div>
                                    <!--end::Menu item-->
                                </div>
                                <!--end::Menu-->
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                    <!--end::Table body-->
                </table>
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    {{$records->links()}}
                </div>
            </div>
        </div>
    </div>
@endsection
@push('customCss')
    <style>
        .intense {
            cursor: url('/assets_backend/media/miei/plus_cursor.png') 25 25, auto;
        }
    </style>
@endpush

@section('customScript')
    <script src="/assets_backend/js-miei/intense.min.js"></script>
    <script>
        $(function () {
            var elements = document.querySelectorAll('.intense');
            Intense(elements);
            $('.update').click(function (e) {
                e.preventDefault();
                let url = $(this).attr('href');
                $.ajax({
                    url: url,
                    type: 'post',
                    dataType: 'json',
                    method: 'PATCH',
                    data: {
                        _token: $('meta[name="_token"]').attr('content')
                    },
                    success: function (resp) {
                        $('#label_' + resp.id).html(resp.label);
                        console.log(resp);
                    }
                });
            });
        })
    </script>
@endsection
