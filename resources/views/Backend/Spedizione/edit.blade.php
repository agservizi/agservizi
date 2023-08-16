@extends('Backend._layout._main')
@section('toolbar')
@endsection
@section('content')
    @php($vecchio=$record->id)
    <div class="card">
        <div class="card-body">
            @include('Backend._components.alertErrori')
            <form method="POST" action="{{action([$controller,'update'],$record->id??'')}}">
                @csrf
                @method($record->id?'PATCH':'POST')
                @if(!$vecchio)
                    <div class="row">
                        <div class="col-md-12">
                            @include('Backend._inputs.inputSelect2',['campo'=>'cliente_id','required'=>false,'label'=>'Cliente','selected'=>'','col' => 2])
                        </div>
                    </div>
                    <div id="dati-nuovo">
                        <h4>Dati nuovo mittente</h4>
                        @include('Backend.Spedizione.datiCliente',['record'=>$record->cliente??new \App\Models\Cliente()])
                    </div>
                @else
                    <div class="row">
                        <div class="col-md-12">
                            @include('Backend._inputs.inputTextReadonly',['campo'=>'cliente','valore'=>$record->cliente->nominativo(),'col' => 2])
                        </div>
                    </div>
                @endif
                <h4>Dati spedizione</h4>
                <div class="row">
                    <div class="col-md-6">
                        @include('Backend._inputs.inputSelect2',['campo'=>'corriere_id','required'=>true,'label'=>'Corriere','selected'=>\App\Models\Corriere::selected(old('corriere_id',$record->corriere_id))])
                    </div>
                    <div class="col-md-6">
                        @include('Backend._inputs.inputSelect2',['campo'=>'servizio_id','required'=>true,'selected'=>\App\Models\Servizio::selected(old('servizio_id',$record->servizio_id)),'label' => 'Servizio'])
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        @include('Backend._inputs.inputTextDataMask',['campo'=>'data_spedizione','autocomplete'=>'off'])
                    </div>

                    <div class="col-md-6">
                        @include('Backend._inputs.inputText',['campo'=>'codice_tracking','autocomplete'=>'off'])
                    </div>
                </div>
                <h4>Dati destinatario</h4>
                <div class="row">
                    <div class="col-md-6">
                        @include('Backend._inputs.inputText',['campo'=>'denominazione_destinatario','autocomplete'=>'off'])
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        @include('Backend._inputs.inputText',['campo'=>'indirizzo_destinatario','autocomplete'=>'off'])
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        @include('Backend._inputs.inputText',['campo'=>'citta_destinatario','autocomplete'=>'off'])
                    </div>
                    <div class="col-md-6">
                        @include('Backend._inputs.inputText',['campo'=>'cap_destinatario','autocomplete'=>'off'])
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        @include('Backend._inputs.inputSelect2',['campo'=>'nazione_destinatario','selected'=>\App\Models\Nazione::selected(old('nazione_destinatario',$record->nazione_destinatario))])
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 offset-md-4 text-center">
                        <button class="btn btn-primary mt-3" type="submit"
                                id="submit">{{$vecchio?'Salva modifiche':'Crea '.\App\Models\Spedizione::NOME_SINGOLARE}}</button>
                    </div>
                    @if($vecchio)
                        <div class="col-md-4 text-end">
                            @if($eliminabile===true)
                                <a class="btn btn-danger mt-3" id="elimina"
                                   href="{{action([$controller,'destroy'],$record->id)}}">Elimina</a>
                            @elseif(is_string($eliminabile))
                                <span data-bs-toggle="tooltip" title="{{$eliminabile}}">
                                    <a class="btn btn-danger mt-3 disabled" href="javascript:void(0)">Elimina</a>
                                </span>
                            @endif
                        </div>
                    @endif
                </div>

            </form>
        </div>
    </div>
@endsection
@push('customScript')
    <script src="/assets_backend/js-miei/select2_it.js"></script>
    <script>
        urlSelect2 = '{{action([\App\Http\Controllers\Backend\Select2::class,'response'])}}';
        $(function () {
            eliminaHandler('Questa Spedizione verrà eliminata definitivamente');

            select2Universale('corriere_id', 'un corriere', 1);

            select2Universale('nazione_destinatario', 'una nazione', 1, 'nazione');
            $('#servizio_id').select2({
                placeholder: 'Seleziona servizio',
                minimumInputLength: 1,
                allowClear: true,
                width: '100%',
                // dropdownParent: $('#modalPosizione'),
                ajax: {
                    quietMillis: 150,
                    url: urlSelect2 + "?servizio_id",
                    dataType: 'json',
                    data: function (term, page) {
                        return {
                            term: term.term,
                            corriere_id: function () {
                                return $('#corriere_id').val()
                            }
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    }
                }
            });

            select2Universale('cliente_id', 'un corriere', 1).on('select2:select', function (e) {
                // Access to full data
                $('#dati-nuovo').hide();

            }).on('select2:clear', function (e) {
                $('#dati-nuovo').show();
            });


        });
    </script>
@endpush
