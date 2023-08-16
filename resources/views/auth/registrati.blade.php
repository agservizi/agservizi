@extends('auth._main')


@section('content')
    @php($nuovo=$record->id)
    <div class="w-lg-1000px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
        @include('Backend._components.alertErrori')
        <form method="POST" action="/register">
            <div class="text-center mb-10">
                <!--begin::Title-->
                <h1 class="text-dark mb-3">Crea un account</h1>
                <!--end::Title-->
                <div class="text-gray-400 fw-bold fs-4">Hai già un account?
                    <a href="{{route('login')}}" class="link-primary fw-bolder">Accedi</a></div>
            </div>
            @csrf
            @method('POST')
            <input type="hidden" name="nazione" value="IT" id="nazione">
            <div class="row">
                <div class="col-md-6">
                    @include("Backend._inputs.inputText",["campo"=>"nome","testo"=>"Nome","required"=>true,"autocomplete"=>"given-name"])
                </div>
                <div class="col-md-6">
                    @include("Backend._inputs.inputText",["campo"=>"cognome","testo"=>"Cognome","required"=>true,"autocomplete"=>"family-name"])
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    @include("Backend._inputs.inputText",["campo"=>"email","testo"=>"Email","required"=>true,"autocomplete"=>"email"])
                </div>
                <div class="col-md-6">
                    @include("Backend._inputs.inputText",["campo"=>"email_confirmation","testo"=>"Conferma email","required"=>true,"autocomplete"=>"off"])
                </div>

            </div>
            <div class="row">
                <div class="col-md-6">
                    @include("Backend._inputs.inputPassword",["campo"=>"password","testo"=>"Password","required"=>true,"autocomplete"=>"new-password",'help'=>'La password deve contenere almeno 8 caratteri'])
                </div>
                <div class="col-md-6">
                    @include("Backend._inputs.inputPassword",["campo"=>"password_confirmation","testo"=>"Conferma password","required"=>true,"autocomplete"=>"new-password"])
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-10 offset-md-2">
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="on" id="flexCheckDefault" name="cookies-privacy-policy"/>
                                <label class="form-check-label fs-5" for="flexCheckDefault">
                                    Accetto le <a href="{{route('cookies-privacy-policy')}}" target="_blank">condizioni  per la privacy</a>. Nessuna informazione verrà resa pubblica.
                                </label>
                            </div>
                            <div class="fv-plugins-message-container invalid-feedback">
                                @error('cookies-privacy-policy')
                                {{$message}}
                                @enderror
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            <div class="row">
                <div class="col-md-4 offset-md-4 text-center">
                    <button class="btn btn-primary mt-3" type="submit">Registrati</button>
                </div>
            </div>

        </form>
    </div>
@endsection
@push('customScript')
    <script>
        $(function () {

            $('#open').click(function () {
                $('#change').trigger('click');
            });
            impostaCampiCitta();

            function impostaCampiCitta() {
                const nazione = $('#nazione').val();
                if (nazione === 'IT') {
                    $('#div_citta').show();
                    $('#citta').prop('required', true);
                    $('#label_citta').addClass('required');
                    $('#div_citta_estera').hide();
                    $('#citta_estera').prop('required', false);
                    $('#label_citta_estera').removeClass('required');

                } else {
                    $('#div_citta_estera').show();
                    $('#citta_estera').prop('required', true);
                    $('#label_citta_estera').addClass('required');

                    $('#div_citta').hide();
                    $('#citta').prop('required', false);
                    $('#label_citta').removeClass('required');

                }
            }


            $('#citta').select2({
                placeholder: 'Seleziona una città',
                minimumInputLength: 3,
                allowClear: true,
                width: '100%',
                // dropdownParent: $('#modalPosizione'),
                ajax: {
                    quietMillis: 150,
                    url: "/select2front?citta",
                    dataType: 'json',
                    data: function (term, page) {
                        return {
                            term: term.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    }
                }
            }).on('select2:select', function (e) {
                // Access to full data
                $("#cap").val(e.params.data.cap);

            }).on('select2:open', function () {
                select2Focus($(this));
            });

        });

        function select2Focus(obj) {
            var id = obj.attr('id');
            id = "select2-" + id + "-results";
            var input = $("[aria-controls='" + id + "']");
            setTimeout(function () {
                input.delay(100).focus().select();
            }, 100);

        }
    </script>
@endpush

