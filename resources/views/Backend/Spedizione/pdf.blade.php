<table style="width: 100%;">
    <tr>
        <td style="width: 50%;">
            <img src="{{public_path('/loghi/logo-ag-grande.png')}}" style="height: 50px;">
        </td>
        <td style="text-align: right; width: 50%; font-size: 8px;">
            {{config('configurazione.tag_title')}}
        </td>
    </tr>
</table>
<table style="padding-top: 20px;">
    <tr>
        <td>Mittente:</td>
        <td style="font-weight: bold;">{{$record->cliente->nominativo()}}</td>
    </tr>
    <tr>
        <td>Data:</td>
        <td style="font-weight: bold;">{{$record->data_spedizione?->format('d/m/Y')}}</td>
    </tr>
    <tr>
        <td>Corriere:</td>
        <td style="font-weight: bold;">{{$record->corriere?->denominazione}}</td>
    </tr>
    <tr>
        <td>Servizio:</td>
        <td style="font-weight: bold;">{{$record->servizio->descrizione}}</td>
    </tr>
    <tr>
        <td>Destinatario:</td>
        <td style="font-weight: bold;">{{$record->denominazione_destinatario}}</td>
    </tr>
    <tr>
        <td>Codice di tracking:</td>
        <td style="font-weight: bold;">{{$record->codice_tracking}}</td>
    </tr>
</table>

<p>

</p>

<table style="width: 100%;">
    <tr>
        <td style="width: 50%;">
            Segui la tua spedizione su app.agenziaplinio.it
        </td>
        <td style="text-align: right; width: 50%; font-size: 8px;">
            <img class="w-200px"
                 src="data:image/png;base64, {{ base64_encode(QrCode::encoding('UTF-8')->format('png')->margin(1)->size(100)->generate('https://app.agenziaplinio.it/backend/cliente/4')) }}">
        </td>
    </tr>
</table>


