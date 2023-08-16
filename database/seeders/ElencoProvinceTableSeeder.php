<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ElencoProvinceTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('elenco_province')->delete();

        \DB::table('elenco_province')->insert(array (
            0 =>
            array (
                'id' => 1,
                'provincia' => 'Torino',
                'sigla_automobilistica' => 'TO',
                'id_regione' => 1,
                'regione' => 'Piemonte',
            ),
            1 =>
            array (
                'id' => 2,
                'provincia' => 'Vercelli',
                'sigla_automobilistica' => 'VC',
                'id_regione' => 1,
                'regione' => 'Piemonte',
            ),
            2 =>
            array (
                'id' => 3,
                'provincia' => 'Novara',
                'sigla_automobilistica' => 'NO',
                'id_regione' => 1,
                'regione' => 'Piemonte',
            ),
            3 =>
            array (
                'id' => 4,
                'provincia' => 'Cuneo',
                'sigla_automobilistica' => 'CN',
                'id_regione' => 1,
                'regione' => 'Piemonte',
            ),
            4 =>
            array (
                'id' => 5,
                'provincia' => 'Asti',
                'sigla_automobilistica' => 'AT',
                'id_regione' => 1,
                'regione' => 'Piemonte',
            ),
            5 =>
            array (
                'id' => 6,
                'provincia' => 'Alessandria',
                'sigla_automobilistica' => 'AL',
                'id_regione' => 1,
                'regione' => 'Piemonte',
            ),
            6 =>
            array (
                'id' => 7,
                'provincia' => 'Valle d\'Aosta/Vallée d\'Aoste',
                'sigla_automobilistica' => 'AO',
                'id_regione' => 2,
                'regione' => 'Valle d\'Aosta/Vallée d\'Aoste',
            ),
            7 =>
            array (
                'id' => 8,
                'provincia' => 'Imperia',
                'sigla_automobilistica' => 'IM',
                'id_regione' => 7,
                'regione' => 'Liguria',
            ),
            8 =>
            array (
                'id' => 9,
                'provincia' => 'Savona',
                'sigla_automobilistica' => 'SV',
                'id_regione' => 7,
                'regione' => 'Liguria',
            ),
            9 =>
            array (
                'id' => 10,
                'provincia' => 'Genova',
                'sigla_automobilistica' => 'GE',
                'id_regione' => 7,
                'regione' => 'Liguria',
            ),
            10 =>
            array (
                'id' => 11,
                'provincia' => 'La Spezia',
                'sigla_automobilistica' => 'SP',
                'id_regione' => 7,
                'regione' => 'Liguria',
            ),
            11 =>
            array (
                'id' => 12,
                'provincia' => 'Varese',
                'sigla_automobilistica' => 'VA',
                'id_regione' => 3,
                'regione' => 'Lombardia',
            ),
            12 =>
            array (
                'id' => 13,
                'provincia' => 'Como',
                'sigla_automobilistica' => 'CO',
                'id_regione' => 3,
                'regione' => 'Lombardia',
            ),
            13 =>
            array (
                'id' => 14,
                'provincia' => 'Sondrio',
                'sigla_automobilistica' => 'SO',
                'id_regione' => 3,
                'regione' => 'Lombardia',
            ),
            14 =>
            array (
                'id' => 15,
                'provincia' => 'Milano',
                'sigla_automobilistica' => 'MI',
                'id_regione' => 3,
                'regione' => 'Lombardia',
            ),
            15 =>
            array (
                'id' => 16,
                'provincia' => 'Bergamo',
                'sigla_automobilistica' => 'BG',
                'id_regione' => 3,
                'regione' => 'Lombardia',
            ),
            16 =>
            array (
                'id' => 17,
                'provincia' => 'Brescia',
                'sigla_automobilistica' => 'BS',
                'id_regione' => 3,
                'regione' => 'Lombardia',
            ),
            17 =>
            array (
                'id' => 18,
                'provincia' => 'Pavia',
                'sigla_automobilistica' => 'PV',
                'id_regione' => 3,
                'regione' => 'Lombardia',
            ),
            18 =>
            array (
                'id' => 19,
                'provincia' => 'Cremona',
                'sigla_automobilistica' => 'CR',
                'id_regione' => 3,
                'regione' => 'Lombardia',
            ),
            19 =>
            array (
                'id' => 20,
                'provincia' => 'Mantova',
                'sigla_automobilistica' => 'MN',
                'id_regione' => 3,
                'regione' => 'Lombardia',
            ),
            20 =>
            array (
                'id' => 21,
                'provincia' => 'Bolzano/Bozen',
                'sigla_automobilistica' => 'BZ',
                'id_regione' => 4,
                'regione' => 'Trentino-Alto Adige/Südtirol',
            ),
            21 =>
            array (
                'id' => 22,
                'provincia' => 'Trento',
                'sigla_automobilistica' => 'TN',
                'id_regione' => 4,
                'regione' => 'Trentino-Alto Adige/Südtirol',
            ),
            22 =>
            array (
                'id' => 23,
                'provincia' => 'Verona',
                'sigla_automobilistica' => 'VR',
                'id_regione' => 5,
                'regione' => 'Veneto',
            ),
            23 =>
            array (
                'id' => 24,
                'provincia' => 'Vicenza',
                'sigla_automobilistica' => 'VI',
                'id_regione' => 5,
                'regione' => 'Veneto',
            ),
            24 =>
            array (
                'id' => 25,
                'provincia' => 'Belluno',
                'sigla_automobilistica' => 'BL',
                'id_regione' => 5,
                'regione' => 'Veneto',
            ),
            25 =>
            array (
                'id' => 26,
                'provincia' => 'Treviso',
                'sigla_automobilistica' => 'TV',
                'id_regione' => 5,
                'regione' => 'Veneto',
            ),
            26 =>
            array (
                'id' => 27,
                'provincia' => 'Venezia',
                'sigla_automobilistica' => 'VE',
                'id_regione' => 5,
                'regione' => 'Veneto',
            ),
            27 =>
            array (
                'id' => 28,
                'provincia' => 'Padova',
                'sigla_automobilistica' => 'PD',
                'id_regione' => 5,
                'regione' => 'Veneto',
            ),
            28 =>
            array (
                'id' => 29,
                'provincia' => 'Rovigo',
                'sigla_automobilistica' => 'RO',
                'id_regione' => 5,
                'regione' => 'Veneto',
            ),
            29 =>
            array (
                'id' => 30,
                'provincia' => 'Udine',
                'sigla_automobilistica' => 'UD',
                'id_regione' => 6,
                'regione' => 'Friuli-Venezia Giulia',
            ),
            30 =>
            array (
                'id' => 31,
                'provincia' => 'Gorizia',
                'sigla_automobilistica' => 'GO',
                'id_regione' => 6,
                'regione' => 'Friuli-Venezia Giulia',
            ),
            31 =>
            array (
                'id' => 32,
                'provincia' => 'Trieste',
                'sigla_automobilistica' => 'TS',
                'id_regione' => 6,
                'regione' => 'Friuli-Venezia Giulia',
            ),
            32 =>
            array (
                'id' => 33,
                'provincia' => 'Piacenza',
                'sigla_automobilistica' => 'PC',
                'id_regione' => 8,
                'regione' => 'Emilia-Romagna',
            ),
            33 =>
            array (
                'id' => 34,
                'provincia' => 'Parma',
                'sigla_automobilistica' => 'PR',
                'id_regione' => 8,
                'regione' => 'Emilia-Romagna',
            ),
            34 =>
            array (
                'id' => 35,
                'provincia' => 'Reggio nell\'Emilia',
                'sigla_automobilistica' => 'RE',
                'id_regione' => 8,
                'regione' => 'Emilia-Romagna',
            ),
            35 =>
            array (
                'id' => 36,
                'provincia' => 'Modena',
                'sigla_automobilistica' => 'MO',
                'id_regione' => 8,
                'regione' => 'Emilia-Romagna',
            ),
            36 =>
            array (
                'id' => 37,
                'provincia' => 'Bologna',
                'sigla_automobilistica' => 'BO',
                'id_regione' => 8,
                'regione' => 'Emilia-Romagna',
            ),
            37 =>
            array (
                'id' => 38,
                'provincia' => 'Ferrara',
                'sigla_automobilistica' => 'FE',
                'id_regione' => 8,
                'regione' => 'Emilia-Romagna',
            ),
            38 =>
            array (
                'id' => 39,
                'provincia' => 'Ravenna',
                'sigla_automobilistica' => 'RA',
                'id_regione' => 8,
                'regione' => 'Emilia-Romagna',
            ),
            39 =>
            array (
                'id' => 40,
                'provincia' => 'Forlì-Cesena',
                'sigla_automobilistica' => 'FC',
                'id_regione' => 8,
                'regione' => 'Emilia-Romagna',
            ),
            40 =>
            array (
                'id' => 41,
                'provincia' => 'Pesaro e Urbino',
                'sigla_automobilistica' => 'PU',
                'id_regione' => 11,
                'regione' => 'Marche',
            ),
            41 =>
            array (
                'id' => 42,
                'provincia' => 'Ancona',
                'sigla_automobilistica' => 'AN',
                'id_regione' => 11,
                'regione' => 'Marche',
            ),
            42 =>
            array (
                'id' => 43,
                'provincia' => 'Macerata',
                'sigla_automobilistica' => 'MC',
                'id_regione' => 11,
                'regione' => 'Marche',
            ),
            43 =>
            array (
                'id' => 44,
                'provincia' => 'Ascoli Piceno',
                'sigla_automobilistica' => 'AP',
                'id_regione' => 11,
                'regione' => 'Marche',
            ),
            44 =>
            array (
                'id' => 45,
                'provincia' => 'Massa-Carrara',
                'sigla_automobilistica' => 'MS',
                'id_regione' => 9,
                'regione' => 'Toscana',
            ),
            45 =>
            array (
                'id' => 46,
                'provincia' => 'Lucca',
                'sigla_automobilistica' => 'LU',
                'id_regione' => 9,
                'regione' => 'Toscana',
            ),
            46 =>
            array (
                'id' => 47,
                'provincia' => 'Pistoia',
                'sigla_automobilistica' => 'PT',
                'id_regione' => 9,
                'regione' => 'Toscana',
            ),
            47 =>
            array (
                'id' => 48,
                'provincia' => 'Firenze',
                'sigla_automobilistica' => 'FI',
                'id_regione' => 9,
                'regione' => 'Toscana',
            ),
            48 =>
            array (
                'id' => 49,
                'provincia' => 'Livorno',
                'sigla_automobilistica' => 'LI',
                'id_regione' => 9,
                'regione' => 'Toscana',
            ),
            49 =>
            array (
                'id' => 50,
                'provincia' => 'Pisa',
                'sigla_automobilistica' => 'PI',
                'id_regione' => 9,
                'regione' => 'Toscana',
            ),
            50 =>
            array (
                'id' => 51,
                'provincia' => 'Arezzo',
                'sigla_automobilistica' => 'AR',
                'id_regione' => 9,
                'regione' => 'Toscana',
            ),
            51 =>
            array (
                'id' => 52,
                'provincia' => 'Siena',
                'sigla_automobilistica' => 'SI',
                'id_regione' => 9,
                'regione' => 'Toscana',
            ),
            52 =>
            array (
                'id' => 53,
                'provincia' => 'Grosseto',
                'sigla_automobilistica' => 'GR',
                'id_regione' => 9,
                'regione' => 'Toscana',
            ),
            53 =>
            array (
                'id' => 54,
                'provincia' => 'Perugia',
                'sigla_automobilistica' => 'PG',
                'id_regione' => 10,
                'regione' => 'Umbria',
            ),
            54 =>
            array (
                'id' => 55,
                'provincia' => 'Terni',
                'sigla_automobilistica' => 'TR',
                'id_regione' => 10,
                'regione' => 'Umbria',
            ),
            55 =>
            array (
                'id' => 56,
                'provincia' => 'Viterbo',
                'sigla_automobilistica' => 'VT',
                'id_regione' => 12,
                'regione' => 'Lazio',
            ),
            56 =>
            array (
                'id' => 57,
                'provincia' => 'Rieti',
                'sigla_automobilistica' => 'RI',
                'id_regione' => 12,
                'regione' => 'Lazio',
            ),
            57 =>
            array (
                'id' => 58,
                'provincia' => 'Roma',
                'sigla_automobilistica' => 'RM',
                'id_regione' => 12,
                'regione' => 'Lazio',
            ),
            58 =>
            array (
                'id' => 59,
                'provincia' => 'Latina',
                'sigla_automobilistica' => 'LT',
                'id_regione' => 12,
                'regione' => 'Lazio',
            ),
            59 =>
            array (
                'id' => 60,
                'provincia' => 'Frosinone',
                'sigla_automobilistica' => 'FR',
                'id_regione' => 12,
                'regione' => 'Lazio',
            ),
            60 =>
            array (
                'id' => 61,
                'provincia' => 'Caserta',
                'sigla_automobilistica' => 'CE',
                'id_regione' => 15,
                'regione' => 'Campania',
            ),
            61 =>
            array (
                'id' => 62,
                'provincia' => 'Benevento',
                'sigla_automobilistica' => 'BN',
                'id_regione' => 15,
                'regione' => 'Campania',
            ),
            62 =>
            array (
                'id' => 63,
                'provincia' => 'Napoli',
                'sigla_automobilistica' => 'NA',
                'id_regione' => 15,
                'regione' => 'Campania',
            ),
            63 =>
            array (
                'id' => 64,
                'provincia' => 'Avellino',
                'sigla_automobilistica' => 'AV',
                'id_regione' => 15,
                'regione' => 'Campania',
            ),
            64 =>
            array (
                'id' => 65,
                'provincia' => 'Salerno',
                'sigla_automobilistica' => 'SA',
                'id_regione' => 15,
                'regione' => 'Campania',
            ),
            65 =>
            array (
                'id' => 66,
                'provincia' => 'L\'Aquila',
                'sigla_automobilistica' => 'AQ',
                'id_regione' => 13,
                'regione' => 'Abruzzo',
            ),
            66 =>
            array (
                'id' => 67,
                'provincia' => 'Teramo',
                'sigla_automobilistica' => 'TE',
                'id_regione' => 13,
                'regione' => 'Abruzzo',
            ),
            67 =>
            array (
                'id' => 68,
                'provincia' => 'Pescara',
                'sigla_automobilistica' => 'PE',
                'id_regione' => 13,
                'regione' => 'Abruzzo',
            ),
            68 =>
            array (
                'id' => 69,
                'provincia' => 'Chieti',
                'sigla_automobilistica' => 'CH',
                'id_regione' => 13,
                'regione' => 'Abruzzo',
            ),
            69 =>
            array (
                'id' => 70,
                'provincia' => 'Campobasso',
                'sigla_automobilistica' => 'CB',
                'id_regione' => 14,
                'regione' => 'Molise',
            ),
            70 =>
            array (
                'id' => 71,
                'provincia' => 'Foggia',
                'sigla_automobilistica' => 'FG',
                'id_regione' => 16,
                'regione' => 'Puglia',
            ),
            71 =>
            array (
                'id' => 72,
                'provincia' => 'Bari',
                'sigla_automobilistica' => 'BA',
                'id_regione' => 16,
                'regione' => 'Puglia',
            ),
            72 =>
            array (
                'id' => 73,
                'provincia' => 'Taranto',
                'sigla_automobilistica' => 'TA',
                'id_regione' => 16,
                'regione' => 'Puglia',
            ),
            73 =>
            array (
                'id' => 74,
                'provincia' => 'Brindisi',
                'sigla_automobilistica' => 'BR',
                'id_regione' => 16,
                'regione' => 'Puglia',
            ),
            74 =>
            array (
                'id' => 75,
                'provincia' => 'Lecce',
                'sigla_automobilistica' => 'LE',
                'id_regione' => 16,
                'regione' => 'Puglia',
            ),
            75 =>
            array (
                'id' => 76,
                'provincia' => 'Potenza',
                'sigla_automobilistica' => 'PZ',
                'id_regione' => 17,
                'regione' => 'Basilicata',
            ),
            76 =>
            array (
                'id' => 77,
                'provincia' => 'Matera',
                'sigla_automobilistica' => 'MT',
                'id_regione' => 17,
                'regione' => 'Basilicata',
            ),
            77 =>
            array (
                'id' => 78,
                'provincia' => 'Cosenza',
                'sigla_automobilistica' => 'CS',
                'id_regione' => 18,
                'regione' => 'Calabria',
            ),
            78 =>
            array (
                'id' => 79,
                'provincia' => 'Catanzaro',
                'sigla_automobilistica' => 'CZ',
                'id_regione' => 18,
                'regione' => 'Calabria',
            ),
            79 =>
            array (
                'id' => 80,
                'provincia' => 'Reggio di Calabria',
                'sigla_automobilistica' => 'RC',
                'id_regione' => 18,
                'regione' => 'Calabria',
            ),
            80 =>
            array (
                'id' => 81,
                'provincia' => 'Trapani',
                'sigla_automobilistica' => 'TP',
                'id_regione' => 19,
                'regione' => 'Sicilia',
            ),
            81 =>
            array (
                'id' => 82,
                'provincia' => 'Palermo',
                'sigla_automobilistica' => 'PA',
                'id_regione' => 19,
                'regione' => 'Sicilia',
            ),
            82 =>
            array (
                'id' => 83,
                'provincia' => 'Messina',
                'sigla_automobilistica' => 'ME',
                'id_regione' => 19,
                'regione' => 'Sicilia',
            ),
            83 =>
            array (
                'id' => 84,
                'provincia' => 'Agrigento',
                'sigla_automobilistica' => 'AG',
                'id_regione' => 19,
                'regione' => 'Sicilia',
            ),
            84 =>
            array (
                'id' => 85,
                'provincia' => 'Caltanissetta',
                'sigla_automobilistica' => 'CL',
                'id_regione' => 19,
                'regione' => 'Sicilia',
            ),
            85 =>
            array (
                'id' => 86,
                'provincia' => 'Enna',
                'sigla_automobilistica' => 'EN',
                'id_regione' => 19,
                'regione' => 'Sicilia',
            ),
            86 =>
            array (
                'id' => 87,
                'provincia' => 'Catania',
                'sigla_automobilistica' => 'CT',
                'id_regione' => 19,
                'regione' => 'Sicilia',
            ),
            87 =>
            array (
                'id' => 88,
                'provincia' => 'Ragusa',
                'sigla_automobilistica' => 'RG',
                'id_regione' => 19,
                'regione' => 'Sicilia',
            ),
            88 =>
            array (
                'id' => 89,
                'provincia' => 'Siracusa',
                'sigla_automobilistica' => 'SR',
                'id_regione' => 19,
                'regione' => 'Sicilia',
            ),
            89 =>
            array (
                'id' => 90,
                'provincia' => 'Sassari',
                'sigla_automobilistica' => 'SS',
                'id_regione' => 20,
                'regione' => 'Sardegna',
            ),
            90 =>
            array (
                'id' => 91,
                'provincia' => 'Nuoro',
                'sigla_automobilistica' => 'NU',
                'id_regione' => 20,
                'regione' => 'Sardegna',
            ),
            91 =>
            array (
                'id' => 92,
                'provincia' => 'Cagliari',
                'sigla_automobilistica' => 'CA',
                'id_regione' => 20,
                'regione' => 'Sardegna',
            ),
            92 =>
            array (
                'id' => 93,
                'provincia' => 'Pordenone',
                'sigla_automobilistica' => 'PN',
                'id_regione' => 6,
                'regione' => 'Friuli-Venezia Giulia',
            ),
            93 =>
            array (
                'id' => 94,
                'provincia' => 'Isernia',
                'sigla_automobilistica' => 'IS',
                'id_regione' => 14,
                'regione' => 'Molise',
            ),
            94 =>
            array (
                'id' => 95,
                'provincia' => 'Oristano',
                'sigla_automobilistica' => 'OR',
                'id_regione' => 20,
                'regione' => 'Sardegna',
            ),
            95 =>
            array (
                'id' => 96,
                'provincia' => 'Biella',
                'sigla_automobilistica' => 'BI',
                'id_regione' => 1,
                'regione' => 'Piemonte',
            ),
            96 =>
            array (
                'id' => 97,
                'provincia' => 'Lecco',
                'sigla_automobilistica' => 'LC',
                'id_regione' => 3,
                'regione' => 'Lombardia',
            ),
            97 =>
            array (
                'id' => 98,
                'provincia' => 'Lodi',
                'sigla_automobilistica' => 'LO',
                'id_regione' => 3,
                'regione' => 'Lombardia',
            ),
            98 =>
            array (
                'id' => 99,
                'provincia' => 'Rimini',
                'sigla_automobilistica' => 'RN',
                'id_regione' => 8,
                'regione' => 'Emilia-Romagna',
            ),
            99 =>
            array (
                'id' => 100,
                'provincia' => 'Prato',
                'sigla_automobilistica' => 'PO',
                'id_regione' => 9,
                'regione' => 'Toscana',
            ),
            100 =>
            array (
                'id' => 101,
                'provincia' => 'Crotone',
                'sigla_automobilistica' => 'KR',
                'id_regione' => 18,
                'regione' => 'Calabria',
            ),
            101 =>
            array (
                'id' => 102,
                'provincia' => 'Vibo Valentia',
                'sigla_automobilistica' => 'VV',
                'id_regione' => 18,
                'regione' => 'Calabria',
            ),
            102 =>
            array (
                'id' => 103,
                'provincia' => 'Verbano-Cusio-Ossola',
                'sigla_automobilistica' => 'VB',
                'id_regione' => 1,
                'regione' => 'Piemonte',
            ),
            103 =>
            array (
                'id' => 104,
                'provincia' => 'Olbia-Tempio',
                'sigla_automobilistica' => 'OT',
                'id_regione' => 20,
                'regione' => 'Sardegna',
            ),
            104 =>
            array (
                'id' => 105,
                'provincia' => 'Ogliastra',
                'sigla_automobilistica' => 'OG',
                'id_regione' => 20,
                'regione' => 'Sardegna',
            ),
            105 =>
            array (
                'id' => 106,
                'provincia' => 'Medio Campidano',
                'sigla_automobilistica' => 'VS',
                'id_regione' => 20,
                'regione' => 'Sardegna',
            ),
            106 =>
            array (
                'id' => 107,
                'provincia' => 'Carbonia-Iglesias',
                'sigla_automobilistica' => 'CI',
                'id_regione' => 20,
                'regione' => 'Sardegna',
            ),
            107 =>
            array (
                'id' => 108,
                'provincia' => 'Monza e della Brianza',
                'sigla_automobilistica' => 'MB',
                'id_regione' => 3,
                'regione' => 'Lombardia',
            ),
            108 =>
            array (
                'id' => 109,
                'provincia' => 'Fermo',
                'sigla_automobilistica' => 'FM',
                'id_regione' => 11,
                'regione' => 'Marche',
            ),
            109 =>
            array (
                'id' => 110,
                'provincia' => 'Barletta-Andria-Trani',
                'sigla_automobilistica' => 'BT',
                'id_regione' => 16,
                'regione' => 'Puglia',
            ),
            110 =>
            array (
                'id' => 111,
                'provincia' => 'Sud Sardegna',
                'sigla_automobilistica' => 'SU',
                'id_regione' => 20,
                'regione' => 'Sardegna',
            ),
        ));


    }
}
