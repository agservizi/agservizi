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
<div class="row">
      <div class="col-md-6">
@include('Backend._inputs.inputText',['campo'=>'nome','required'=>true,'autocomplete'=>'off'])
       </div>
</div>
<div class="row">
      <div class="col-md-6">
@include('Backend._inputs.inputText',['campo'=>'cognome','required'=>true,'autocomplete'=>'off'])
       </div>
</div>
<div class="row">
      <div class="col-md-6">
@include('Backend._inputs.inputText',['campo'=>'email','required'=>true,'autocomplete'=>'off'])
       </div>
</div>
<div class="row">
      <div class="col-md-6">
@include('Backend._inputs.inputText',['campo'=>'email_verified_at','autocomplete'=>'off'])
       </div>
</div>
<div class="row">
      <div class="col-md-6">
@include('Backend._inputs.inputText',['campo'=>'password','required'=>true,'autocomplete'=>'off'])
       </div>
</div>
<div class="row">
      <div class="col-md-12">
@include('Backend._inputs.inputTextAreaCol',['campo'=>'two_factor_secret','col'=>2])
       </div>
</div>
<div class="row">
      <div class="col-md-12">
@include('Backend._inputs.inputTextAreaCol',['campo'=>'two_factor_recovery_codes','col'=>2])
       </div>
</div>
<div class="row">
      <div class="col-md-6">
@include('Backend._inputs.inputText',['campo'=>'two_factor_confirmed_at','autocomplete'=>'off'])
       </div>
</div>
<div class="row">
      <div class="col-md-6">
@include('Backend._inputs.inputText',['campo'=>'remember_token','autocomplete'=>'off'])
       </div>
</div>
<div class="row">
      <div class="col-md-6">
@include('Backend._inputs.inputText',['campo'=>'ultimo_accesso','autocomplete'=>'off'])
       </div>
</div>
<div class="row">
      <div class="col-md-6">
@include('Backend._inputs.inputText',['campo'=>'telefono','autocomplete'=>'off'])
       </div>
</div>
<div class="row">
      <div class="col-md-6">
@include('Backend._inputs.inputText',['campo'=>'ruolo','autocomplete'=>'off'])
       </div>
</div>

                <div class="row">
                    <div class="col-md-4 offset-md-4 text-center">
                        <button class="btn btn-primary mt-3" type="submit" id="submit">{{$vecchio?'Salva modifiche':'Crea '.\App\Models\Cliente::NOME_SINGOLARE}}</button>
                    </div>
                    @if($vecchio)
                        <div class="col-md-4 text-end">
                            @if($eliminabile===true)
                                <a class="btn btn-danger mt-3" id="elimina" href="{{action([$controller,'destroy'],$record->id)}}">Elimina</a>
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
@push('customScript')<script>
 $(function (){
            eliminaHandler('Questo Cliente verrà eliminata definitivamente');

});
</script>
@endpush