<?php

return [


    'tag_title' => 'Area Clienti',
    'log_rocket' => '',

    'mostra_accessi_test'=>false,
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
        'cartella' => '/loghi_corrieri',
        'dimensioni' => [
            'width' => 540,
            'height' => 360
        ]
    ],
    'allegati_spedizioni' => [
        'cartella' => '/allegati_spedizioni',
    ],


];
