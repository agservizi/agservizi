@extends('Backend._layout._main')
@section('toolbar')
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{action([$controller,'update'],$record->id??'')}}">
                @csrf
                @method($record->id?'PATCH':'POST')

                @include('Backend._inputs.inputText',['campo'=>'email','required'=>true,'autocomplete'=>'off'])
                @include('Backend._inputs.inputText',['campo'=>'cognome','required'=>true,'autocomplete'=>'off'])
                @include('Backend._inputs.inputText',['campo'=>'nome','required'=>true,'autocomplete'=>'off'])
                @include('Backend._inputs.inputText',['campo'=>'telefono','required'=>false,'autocomplete'=>'off','help'=>'Se non presente il prefisso internazionale, verrà aggiunto +39. Eventuali spazi verranno rimossi in modo automatico'])
                @if(count($ruoli))
                    <div class="row mb-6">
                        <div class="col-lg-4 col-form-label text-lg-end">
                            <label class="fw-bold fs-6  required ">Ruolo</label><br>
                            <a href="javascript:void(0)" id="kt_drawer_example_basic_button" class=""><i class="fas fa-question-circle ms-1 fs-6 "></i></a>
                        </div>
                        <div class="col-lg-8 fv-row fv-plugins-icon-container">
                            @foreach($ruoli as $ruolo)
                                <div class="form-check form-check-custom form-check-solid my-3">
                                    <input class="form-check-input ruolo" name="ruolo" type="radio" id="radio{{$ruolo}}"
                                           value="{{$ruolo->value}}" {{$record->hasPermissionTo($ruolo->value)?'checked':''}} required/>
                                    <label class="form-check-label" for="radio{{$ruolo->value}}">{{$ruolo->testo()}}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @includeWhen(false,'Backend._inputs.inputSwitch',['campo'=>'operatori-vedi_tutti','testo'=>'Vedi tutti gli operatori','required'=>false,'help'=>'Vede tutti gli operatori'])
                @else
                    <input type="hidden" name="ruolo" value="operatore">
                @endif
                @if(!$record->id)
                    <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">

                        <!--begin::Icon-->
                        <!--begin::Svg Icon | path: assets/media/icons/duotone/Communication/Mail-notification.svg-->
                        <span class="svg-icon svg-icon-2tx svg-icon-primary me-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <path
                                        d="M21,12.0829584 C20.6747915,12.0283988 20.3407122,12 20,12 C16.6862915,12 14,14.6862915 14,18 C14,18.3407122 14.0283988,18.6747915 14.0829584,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,8 C3,6.8954305 3.8954305,6 5,6 L19,6 C20.1045695,6 21,6.8954305 21,8 L21,12.0829584 Z M18.1444251,7.83964668 L12,11.1481833 L5.85557487,7.83964668 C5.4908718,7.6432681 5.03602525,7.77972206 4.83964668,8.14442513 C4.6432681,8.5091282 4.77972206,8.96397475 5.14442513,9.16035332 L11.6444251,12.6603533 C11.8664074,12.7798822 12.1335926,12.7798822 12.3555749,12.6603533 L18.8555749,9.16035332 C19.2202779,8.96397475 19.3567319,8.5091282 19.1603533,8.14442513 C18.9639747,7.77972206 18.5091282,7.6432681 18.1444251,7.83964668 Z"
                                        fill="#000000"/>
                                <circle fill="#000000" opacity="0.3" cx="19.5" cy="17.5" r="2.5"/>
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                        <!--end::Icon-->
                        <!--begin::Wrapper-->
                        <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                            <!--begin::Content-->
                            <div class="mb-3 mb-md-0 fw-bold">
                                <h4 class="text-gray-900 fw-bolder">Invio email</h4>
                                <div class="fs-6 text-gray-700 pe-7">Verrà inviata una mail all'indirizzo dell'operatore con il link per impostare la password di accesso</div>
                            </div>
                            <!--end::Content-->
                            <!--begin::Action-->
                            <button class="btn btn-primary mt-3" type="submit">{{$record->id?'Salva modifiche':'Crea'}}</button>
                            <!--end::Action-->
                        </div>
                        <!--end::Wrapper-->
                    </div>

                @else
                    <div class="row">
                        <div class="col-md-4 offset-md-4">
                            <button class="btn btn-primary mt-3" type="submit">{{$record->id?'Salva modifiche':'Crea'}}</button>
                        </div>
                        <div class="col-md-4 text-end">
                            @if($eliminabile===true)
                                <a class="btn btn-danger mt-3" id="elimina" href="{{action([$controller,'destroy'],$record->id)}}">Elimina</a>
                            @elseif(is_string($eliminabile))
                                <span data-bs-toggle="tooltip" title="{{$eliminabile}}">
                                    <a class="btn btn-danger mt-3 disabled" href="javascript:void(0)">Elimina</a>
                                </span>
                            @endif
                            <button type="submit" class="btn btn-danger mt-3" name="sospendi"
                                    href="{{action([\App\Http\Controllers\Backend\OperatoreController::class,'azioni'],['id'=>$record->id,'azione'=>'sospendi'])}}">Sospendi
                            </button>
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>
    @if(false)
        <div
                id="kt_drawer_example_basic"

                class="bg-white"
                data-kt-drawer="true"
                data-kt-drawer-activate="true"
                data-kt-drawer-toggle="#kt_drawer_example_basic_button"
                data-kt-drawer-close="#kt_drawer_example_basic_close"
                data-kt-drawer-width="700px"
        >
            <div class="card rounded-0 w-100">
                <!--begin::Card header-->
                <div class="card-header pe-5">
                    <!--begin::Title-->
                    <div class="card-title">
                        <!--begin::User-->
                        Ruoli
                        <!--end::User-->
                    </div>
                    <!--end::Title-->
                    <!--begin::Card toolbar-->
                    <div class="card-toolbar">
                        <!--begin::Close-->
                        <div class="btn btn-sm btn-icon btn-active-light-primary" id="kt_drawer_example_basic_close">
                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                            <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)"
                                      fill="black"></rect>
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="black"></rect>
                            </svg>
                        </span>
                            <!--end::Svg Icon-->
                        </div>
                        <!--end::Close-->
                    </div>
                    <!--end::Card toolbar-->
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body hover-scroll-overlay-y">
                    <table class="table table-row-bordered">
                        <thead>
                        <tr class="fw-bolder fs-6 text-gray-800">
                            <th style="width: 20%;"></th>
                            <th class="text-center" style="width: 20%;"><span
                                        class="badge badge-light-{{config('configurazione.ruoli.operatore.colore')}} fw-bolder">{{config('configurazione.ruoli.operatore.testo')}}</span>
                            </th>
                            <th class="text-center" style="width: 20%;"><span
                                        class="badge badge-light-{{config('configurazione.ruoli.teamleader.colore')}} fw-bolder">{{config('configurazione.ruoli.teamleader.testo')}}</span>
                            </th>
                            <th class="text-center" style="width: 20%;"><span
                                        class="badge badge-light-{{config('configurazione.ruoli.supervisore.colore')}} fw-bolder">{{config('configurazione.ruoli.supervisore.testo')}}</span>
                            </th>
                            <th class="text-center" style="width: 20%;"><span class="badge badge-light-info fw-bolder">Admin</span></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="fw-bolder fs-6 text-gray-800">Vede operatori</td>
                            <td class="text-center">No</td>
                            <td class="text-center">Operatori assegnati</td>
                            <td class="text-center">Tutti</td>
                            <td class="text-center">Tutti</td>
                        </tr>
                        <tr>
                            <td class="fw-bolder fs-6 text-gray-800">Crea operatori</td>
                            <td class="text-center">No</td>
                            <td class="text-center">Operatori assegnati</td>
                            <td class="text-center">Tutti</td>
                            <td class="text-center">Tutti</td>
                        </tr>
                        <tr>
                            <td class="fw-bolder fs-6 text-gray-800">Ordini</td>
                            <td class="text-center">Solo propri</td>
                            <td class="text-center">Degli propri e di operatori assegnati</td>
                            <td class="text-center">Tutti</td>
                            <td class="text-center">Tutti</td>
                        </tr>
                        <tr>
                            <td class="fw-bolder fs-6 text-gray-800">Imposta stato</td>
                            <td class="text-center">No</td>
                            <td class="text-center">Solo sigillato/non sigillato</td>
                            <td class="text-center">Solo sigillato/non sigillato</td>
                            <td class="text-center">Tutti</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <!--end::Card body-->
                <!--begin::Card footer-->
                <div class="card-footer">
                    <!--begin::Dismiss button-->
                    <button class="btn btn-light-danger" data-kt-drawer-dismiss="true">Dismiss drawer</button>
                    <!--end::Dismiss button-->
                </div>
                <!--end::Card footer-->
            </div>
        </div>
    @endif
@endsection
@push('customScript')

    <script>


        $(function () {
            eliminaHandler("Questo operatore verrà eliminato definitivamente");

        })
    </script>
@endpush
