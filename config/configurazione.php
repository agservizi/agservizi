<?php

return [


    'tag_title' => 'Area Clienti',
    'log_rocket' => '',

    'mostra_accessi_test'=>true,
    'accessi_test' => [
        ['descrizione' => 'Admin', 'email' => 'admin@admin.com', 'password' => 'password'],
    ],
    'url_online'=>null,

    'primoAnno' => 2023,

    'cartella_progetto' => env('APP_NAME'),

    'aliquota_iva' => 22,

    'cssCKEditor' => [
    ],

    'loghi_corrieri' => [
        'cartella' => '/immagini',
        'dimensioni' => [
            'width' => 540,
            'height' => 540
        ]
    ],


];
