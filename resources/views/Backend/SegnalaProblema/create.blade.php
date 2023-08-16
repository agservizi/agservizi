@extends('Backend._components.modal')
@section('content')
    @include('Backend._components.alertErrori')
    <div class="alert alert-success " id="problema-alert" style="display: none;">
        <strong>Fatto: </strong> la tua segnalazione è stata inviata.
    </div>
    <div class="alert alert-info " id="problema-incorso" style="display: none;">
        <strong>Attendi: </strong> invio segnalazione in corso
    </div>
    <div id="problema-form">
        <div id="screenshotimg" style="text-align: center; border-style: solid; border-color: rgb(239,242,245); border-width: 2px;"></div>
        <p class="text-muted">Questo screen-shot verrà allegato alla segnalazione</p>
        <form id="segnala-form" class="form-horizontal" method="POST"
              action="{{action([\App\Http\Controllers\SegnalaProblemaController::class,'store'])}}">
            @csrf
            @include('Backend._inputs.inputTextV',['campo'=>'titolo','label'=>'Oggetto','required'=>true,'autocomplete'=>'off'])
            @include('Backend._inputs.inputTextArea',['campo'=>'descrizione','required'=>true,'autocomplete'=>'off'])
            <div class="w-100 text-center">
                <input type="submit" value="Invia segnalazione" class="btn btn-primary">
            </div>
            <!-- /.col-md-4 -->
        </form>
    </div>
@endsection
@section('customScript')
    <script>
        $(function () {
            $('#segnala-form').submit(function (e) {
                var url = $(this).attr('action');
                e.preventDefault();
                $('#problema-form').hide();
                $('#problema-incorso').show();

                var jpegUrl = screenShotImg.toDataURL();

                $.ajax({
                    url: url,
                    type: 'post',
                    dataType: 'json',
                    method: 'POST',
                    data: {
                        _token: $('meta[name="_token"]').attr('content'),
                        base64data: jpegUrl,
                        titolo: $('#titolo').val(),
                        descrizione: $('#descrizione').val(),
                        url: document.URL,
                        urgenza: $('input[name=urgenza]:checked').val()

                    },
                    success: function (resp) {
                        $('#problema-incorso').hide();
                        $('#problema-alert').show();
                        //$('#ajax').modal('toggle');
                    }
                });

            });
        });
    </script>
@endsection
