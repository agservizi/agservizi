@extends('Backend._components.modal')
@section('content')

    <form id="form-cambia-stato"
          action="{{action([\App\Http\Controllers\Backend\SpedizioneController::class,'updateStato'],$record->id)}}"
          method="POST"
    >
        @method('PATCH')
        @csrf
        <div class="row mb-6">
            <!--begin::Heading-->
            <div class="col-lg-4 col-form-label text-lg-end">
                <label class="fw-bold fs-6  required">Stato spedizione</label>
            </div>
            <!--end::Heading-->
            <!--begin::Row-->
            @php($selected=old('stato_spedizione',$record->stato_spedizione))
            <div class="col-lg-8 fv-row fv-plugins-icon-container">
                <!--begin::Radio group-->
                <div class="btn-group" data-kt-buttons="true" data-kt-buttons-target="[data-kt-button]">
                    @foreach(\App\Models\StatoSpedizione::get() as $stato)
                        <label
                            class="btn btn-outline-dark  btn-active-primary {{$selected==$stato->id?'active':''}}" style="background-color: {{$stato->colore_hex}};"
                            data-kt-button="true" style="padding: .4rem 1rem">
                            <input class="btn-check buttons" type="radio" name="stato_spedizione" value="{{$stato->id}}"
                                   {{$selected==$stato->id?'checked':''}} id="stato{{$stato->id}}"
                                   />
                            {{$stato->nome}}
                        </label>
                    @endforeach
                </div>
                <div class="fv-plugins-message-container invalid-feedback">

                </div>
            </div>
        </div>
    </form>
    <script>
        $(function () {
            $('.btn-check').click(function () {

                formSubmit($('#form-cambia-stato'));
            });
        });
    </script>
@endsection
