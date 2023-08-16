<strong>Segnalazione anomalia da {{$nominativo}}</strong>
@isset($dati['sito'])
    <p>
        <strong>Sito:</strong>{{$dati['sito']}}
    </p>
@endisset
<p>
    <strong>Titolo:</strong>{{$dati['titolo']}}
</p>
<p>
    <strong>Messaggio:</strong>{{$dati['descrizione']}}
</p>

@isset($dati['url'])
    <p>
        <strong>Url della pagina:</strong> <a href="{{$dati['url']}}">{{$dati['url']}}</a>
    </p>
@endisset

<img src="<?php echo $message->embedData($immagine, 'Errore.png'); ?>">
