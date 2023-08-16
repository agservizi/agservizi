@extends('auth._main')


@section('content')
    <div class="w-lg-1000px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
        @include('Backend._components.alertErrori')
        <form method="POST" action="{{action([\App\Http\Controllers\Backend\CompletaRegistrazione::class,'update'])}}">
            <div class="text-center mb-10">
                <h1 class="text-dark mb-3">Completa la tua anagrafica</h1>
            </div>
            @csrf
            @method('POST')
            <input type="hidden" name="nazione" value="IT" id="nazione">

            <div class="row">
                <div class="col-md-6">
                    @include("Backend._inputs.inputTextReadonly",["campo"=>"name","testo"=>"Nome",])
                </div>
                <div class="col-md-6">
                    @include("Backend._inputs.inputTextReadonly",["campo"=>"cognome","testo"=>"Cognome"])
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    @include("Backend._components_.inputTelefono",["campo"=>"telefono","testo"=>"Cellulare","required"=>true,"autocomplete"=>"tel"])
                </div>

            </div>

            <div class="row">
                <div class="col-md-6">
                    @include("Backend._inputs.inputSelect2",["campo"=>"citta","testo"=>"Città","required"=>false,"autocomplete"=>"off",'selected'=>\App\Models\Comune::selected(old('citta',$record->citta))])
                    @include("Backend._inputs.inputText",["campo"=>"citta_estera","testo"=>"Citta","required"=>false,"autocomplete"=>"off"])
                </div>
                <div class="col-md-6">
                    @include("Backend._inputs.inputText",["campo"=>"cap","testo"=>"CAP","required"=>true,"autocomplete"=>"postal-code"])
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    @include("Backend._inputs.inputText",["campo"=>"indirizzo","testo"=>"Indirizzo","required"=>true,"autocomplete"=>"street-address",'col'=>2])
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    @include("Backend._inputs.inputTextData",["campo"=>"data_di_nascita","testo"=>"Data di nascita","required"=>true,"autocomplete"=>"off"])
                </div>
                <div class="col-md-6">
                    @include("Backend._inputs.inputSelect2",["campo"=>"luogo_nascita","testo"=>"Luogo di nascita","required"=>false,'selected'=>\App\Models\Comune::selected(old('luogo_nascita',$record->luogo_nascita))])
                </div>

            </div>
            <div class="row">
                <div class="col-md-6">
                    @include("Backend._inputs.inputRadioH",["campo"=>"genere","testo"=>"Sesso","required"=>false,"autocomplete"=>"off",'array'=>['m'=>'Maschio','F'=>'Femmina'],'help'=>'Campo facoltativo'])
                </div>
                <div class="col-md-6">
                    @include("Backend._inputs.inputText",["campo"=>"codice_fiscale","testo"=>"Codice Fiscale","required"=>false,"autocomplete"=>"off",'help'=>'Campo facoltativo'])
                </div>
                @if(false)
                    <div class="col-md-6">
                        @include("Backend._inputs.inputText",["campo"=>"partita_iva","testo"=>"Partita IVA","required"=>true,"autocomplete"=>"off"])
                    </div>
                @endif
            </div>

            <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                    <div class="mb-3 mb-md-0 fw-bold">
                        <h4 class="text-gray-900 fw-bolder">Attenzione</h4>
                        <div class="fs-6 text-gray-700 pe-7">Successivamente non sarà piossibile modificare i tuoi dati anagrafici</div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-4 offset-md-4 text-center">
                    <button class="btn btn-primary mt-3" type="submit">Salva i tuoi dati</button>
                </div>
            </div>
        </form>
    </div>
@endsection
@push('customScript')

    <script>
        $(function () {
            moment.locale('it');

            let startPicker = $("#data_di_nascita").flatpickr({
                locale: 'it',
                dateFormat: 'd/m/Y',
                maxDate: "today",
                enableTime: false,
                time_24hr: true,
                confirmDate: true,
                minTime: "08:00",
                maxTime: "22:00",
                //plugins: [new confirmDatePlugin({})],
                onChange: function () {
                },
                onClose: function (selectedDates, dateStr, instance) {
                    endPicker.set('minDate', dateStr);
                }
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
            $('#luogo_nascita').select2({
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

