<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ElencoNazioniTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('elenco_nazioni')->delete();
        
        \DB::table('elenco_nazioni')->insert(array (
            0 => 
            array (
                'alpha2' => 'AD',
                'alpha3' => 'AND',
                'langEN' => 'Andorra',
                'langIT' => 'Andorra',
                'nazionalitaEN' => 'Andorran',
                'nazionalitaIT' => 'Andorra',
            ),
            1 => 
            array (
                'alpha2' => 'AE',
                'alpha3' => 'ARE',
                'langEN' => 'United Arab Emirates',
                'langIT' => 'Emirati Arabi Uniti',
                'nazionalitaEN' => 'Emirati, Emirian, Emiri',
                'nazionalitaIT' => 'Emirati, Emirati Arabi, Emirati Arabi Uniti',
            ),
            2 => 
            array (
                'alpha2' => 'AF',
                'alpha3' => 'AFG',
                'langEN' => 'Afghanistan',
                'langIT' => 'Afghanistan',
                'nazionalitaEN' => 'Afghan',
                'nazionalitaIT' => 'Afgano',
            ),
            3 => 
            array (
                'alpha2' => 'AG',
                'alpha3' => 'ATG',
                'langEN' => 'Antigua and Barbuda',
                'langIT' => 'Antigua e Barbuda',
                'nazionalitaEN' => 'Antiguan or Barbudan',
                'nazionalitaIT' => 'Antigua e Barbudan',
            ),
            4 => 
            array (
                'alpha2' => 'AI',
                'alpha3' => 'AIA',
                'langEN' => 'Anguilla',
                'langIT' => 'Anguilla',
                'nazionalitaEN' => 'Anguillan',
                'nazionalitaIT' => 'Anguillan',
            ),
            5 => 
            array (
                'alpha2' => 'AL',
                'alpha3' => 'ALB',
                'langEN' => 'Albania',
                'langIT' => 'Albania',
                'nazionalitaEN' => 'Albanian',
                'nazionalitaIT' => 'Albanese',
            ),
            6 => 
            array (
                'alpha2' => 'AM',
                'alpha3' => 'ARM',
                'langEN' => 'Armenia',
                'langIT' => 'Armenia',
                'nazionalitaEN' => 'Armenian',
                'nazionalitaIT' => 'Armeno',
            ),
            7 => 
            array (
                'alpha2' => 'AN',
                'alpha3' => 'ANT',
                'langEN' => 'Netherlands Antilles',
                'langIT' => 'Antille Olandesi',
                'nazionalitaEN' => NULL,
                'nazionalitaIT' => NULL,
            ),
            8 => 
            array (
                'alpha2' => 'AO',
                'alpha3' => 'AGO',
                'langEN' => 'Angola',
                'langIT' => 'Angola',
                'nazionalitaEN' => 'Angolan',
                'nazionalitaIT' => 'Angolano',
            ),
            9 => 
            array (
                'alpha2' => 'AQ',
                'alpha3' => 'ATA',
                'langEN' => 'Antarctica',
                'langIT' => 'Antartide',
                'nazionalitaEN' => 'Antarctic',
                'nazionalitaIT' => 'Antartico',
            ),
            10 => 
            array (
                'alpha2' => 'AR',
                'alpha3' => 'ARG',
                'langEN' => 'Argentina',
                'langIT' => 'Argentina',
                'nazionalitaEN' => 'Argentine',
                'nazionalitaIT' => 'Argentino',
            ),
            11 => 
            array (
                'alpha2' => 'AS',
                'alpha3' => 'ASM',
                'langEN' => 'American Samoa',
                'langIT' => 'Samoa Americane',
                'nazionalitaEN' => 'American Samoan',
                'nazionalitaIT' => 'Samoano americano',
            ),
            12 => 
            array (
                'alpha2' => 'AT',
                'alpha3' => 'AUT',
                'langEN' => 'Austria',
                'langIT' => 'Austria',
                'nazionalitaEN' => 'Austrian',
                'nazionalitaIT' => 'Austriaco',
            ),
            13 => 
            array (
                'alpha2' => 'AU',
                'alpha3' => 'AUS',
                'langEN' => 'Australia',
                'langIT' => 'Australia',
                'nazionalitaEN' => 'Australian',
                'nazionalitaIT' => 'Australiano',
            ),
            14 => 
            array (
                'alpha2' => 'AW',
                'alpha3' => 'ABW',
                'langEN' => 'Aruba',
                'langIT' => 'Aruba',
                'nazionalitaEN' => 'Aruban',
                'nazionalitaIT' => 'Aruba',
            ),
            15 => 
            array (
                'alpha2' => 'AX',
                'alpha3' => 'ALA',
                'langEN' => 'Åland Islands',
                'langIT' => 'Åland Islands',
                'nazionalitaEN' => 'Åland Island',
                'nazionalitaIT' => 'Land Island',
            ),
            16 => 
            array (
                'alpha2' => 'AZ',
                'alpha3' => 'AZE',
                'langEN' => 'Azerbaijan',
                'langIT' => 'Azerbaijan',
                'nazionalitaEN' => 'Azerbaijani, Azeri',
                'nazionalitaIT' => 'Azero, azero',
            ),
            17 => 
            array (
                'alpha2' => 'BA',
                'alpha3' => 'BIH',
                'langEN' => 'Bosnia and Herzegovina',
                'langIT' => 'Bosnia Erzegovina',
                'nazionalitaEN' => 'Bosnian or Herzegovinian',
                'nazionalitaIT' => 'Bosniaco ed Erzegovina',
            ),
            18 => 
            array (
                'alpha2' => 'BB',
                'alpha3' => 'BRB',
                'langEN' => 'Barbados',
                'langIT' => 'Barbados',
                'nazionalitaEN' => 'Barbadian',
                'nazionalitaIT' => 'Barbados',
            ),
            19 => 
            array (
                'alpha2' => 'BD',
                'alpha3' => 'BGD',
                'langEN' => 'Bangladesh',
                'langIT' => 'Bangladesh',
                'nazionalitaEN' => 'Bangladeshi',
                'nazionalitaIT' => 'Bengalese',
            ),
            20 => 
            array (
                'alpha2' => 'BE',
                'alpha3' => 'BEL',
                'langEN' => 'Belgium',
                'langIT' => 'Belgio',
                'nazionalitaEN' => 'Belgian',
                'nazionalitaIT' => 'Belga',
            ),
            21 => 
            array (
                'alpha2' => 'BF',
                'alpha3' => 'BFA',
                'langEN' => 'Burkina Faso',
                'langIT' => 'Burkina Faso',
                'nazionalitaEN' => 'Burkinabé',
                'nazionalitaIT' => 'Burkinabé',
            ),
            22 => 
            array (
                'alpha2' => 'BG',
                'alpha3' => 'BGR',
                'langEN' => 'Bulgaria',
                'langIT' => 'Bulgaria',
                'nazionalitaEN' => 'Bulgarian',
                'nazionalitaIT' => 'Bulgaro',
            ),
            23 => 
            array (
                'alpha2' => 'BH',
                'alpha3' => 'BHR',
                'langEN' => 'Bahrain',
                'langIT' => 'Bahrain',
                'nazionalitaEN' => 'Bahraini',
                'nazionalitaIT' => 'Bahrein',
            ),
            24 => 
            array (
                'alpha2' => 'BI',
                'alpha3' => 'BDI',
                'langEN' => 'Burundi',
                'langIT' => 'Burundi',
                'nazionalitaEN' => 'Burundian',
                'nazionalitaIT' => 'Burundi',
            ),
            25 => 
            array (
                'alpha2' => 'BJ',
                'alpha3' => 'BEN',
                'langEN' => 'Benin',
                'langIT' => 'Benin',
                'nazionalitaEN' => 'Beninese, Beninois',
                'nazionalitaIT' => 'Beninese',
            ),
            26 => 
            array (
                'alpha2' => 'BM',
                'alpha3' => 'BMU',
                'langEN' => 'Bermuda',
                'langIT' => 'Bermuda',
                'nazionalitaEN' => 'Bermudian, Bermudan',
                'nazionalitaIT' => 'Bermuda, Bermuda',
            ),
            27 => 
            array (
                'alpha2' => 'BN',
                'alpha3' => 'BRN',
                'langEN' => 'Brunei Darussalam',
                'langIT' => 'Brunei Darussalam',
                'nazionalitaEN' => 'Bruneian',
                'nazionalitaIT' => 'Brunei',
            ),
            28 => 
            array (
                'alpha2' => 'BO',
                'alpha3' => 'BOL',
                'langEN' => 'Bolivia',
                'langIT' => 'Bolivia',
                'nazionalitaEN' => 'Bolivian',
                'nazionalitaIT' => 'Boliviano',
            ),
            29 => 
            array (
                'alpha2' => 'BR',
                'alpha3' => 'BRA',
                'langEN' => 'Brazil',
                'langIT' => 'Brasile',
                'nazionalitaEN' => 'Brazilian',
                'nazionalitaIT' => 'Brasiliano',
            ),
            30 => 
            array (
                'alpha2' => 'BS',
                'alpha3' => 'BHS',
                'langEN' => 'Bahamas',
                'langIT' => 'Bahamas',
                'nazionalitaEN' => 'Bahamian',
                'nazionalitaIT' => 'Bahamas',
            ),
            31 => 
            array (
                'alpha2' => 'BT',
                'alpha3' => 'BTN',
                'langEN' => 'Bhutan',
                'langIT' => 'Bhutan',
                'nazionalitaEN' => 'Bhutanese',
                'nazionalitaIT' => 'Bhutanesi',
            ),
            32 => 
            array (
                'alpha2' => 'BV',
                'alpha3' => 'BVT',
                'langEN' => 'Bouvet Island',
                'langIT' => 'Isola di Bouvet',
                'nazionalitaEN' => 'Bouvet Island',
                'nazionalitaIT' => 'Isola Bouvet',
            ),
            33 => 
            array (
                'alpha2' => 'BW',
                'alpha3' => 'BWA',
                'langEN' => 'Botswana',
                'langIT' => 'Botswana',
                'nazionalitaEN' => 'Motswana, Botswanan',
                'nazionalitaIT' => 'Motswana, Botswana',
            ),
            34 => 
            array (
                'alpha2' => 'BY',
                'alpha3' => 'BLR',
                'langEN' => 'Belarus',
                'langIT' => 'Bielorussia',
                'nazionalitaEN' => 'Belarusian',
                'nazionalitaIT' => 'Bielorusso',
            ),
            35 => 
            array (
                'alpha2' => 'BZ',
                'alpha3' => 'BLZ',
                'langEN' => 'Belize',
                'langIT' => 'Belize',
                'nazionalitaEN' => 'Belizean',
                'nazionalitaIT' => 'Belize',
            ),
            36 => 
            array (
                'alpha2' => 'CA',
                'alpha3' => 'CAN',
                'langEN' => 'Canada',
                'langIT' => 'Canada',
                'nazionalitaEN' => 'Canadian',
                'nazionalitaIT' => 'Canadese',
            ),
            37 => 
            array (
                'alpha2' => 'CC',
                'alpha3' => 'CCK',
            'langEN' => 'Cocos (Keeling) Islands',
                'langIT' => 'Isole Cocos',
                'nazionalitaEN' => 'Cocos Island',
                'nazionalitaIT' => 'Isola di Cocos',
            ),
            38 => 
            array (
                'alpha2' => 'CD',
                'alpha3' => 'COD',
                'langEN' => 'The Democratic Republic Of The Congo',
                'langIT' => 'Repubblica Democratica del Congo',
                'nazionalitaEN' => 'Congolese',
                'nazionalitaIT' => 'Congolese',
            ),
            39 => 
            array (
                'alpha2' => 'CF',
                'alpha3' => 'CAF',
                'langEN' => 'Central African',
                'langIT' => 'Repubblica Centroafricana',
                'nazionalitaEN' => 'Central African',
                'nazionalitaIT' => 'Centroafricano',
            ),
            40 => 
            array (
                'alpha2' => 'CG',
                'alpha3' => 'COG',
                'langEN' => 'Republic of the Congo',
                'langIT' => 'Repubblica del Congo',
                'nazionalitaEN' => 'Congolese',
                'nazionalitaIT' => 'Congolese',
            ),
            41 => 
            array (
                'alpha2' => 'CH',
                'alpha3' => 'CHE',
                'langEN' => 'Switzerland',
                'langIT' => 'Svizzera',
                'nazionalitaEN' => 'Swiss',
                'nazionalitaIT' => 'Svizzero',
            ),
            42 => 
            array (
                'alpha2' => 'CI',
                'alpha3' => 'CIV',
                'langEN' => 'Côte d\'Ivoire',
                'langIT' => 'Costa d\'Avorio',
                'nazionalitaEN' => 'Ivorian',
                'nazionalitaIT' => 'Ivoriano',
            ),
            43 => 
            array (
                'alpha2' => 'CK',
                'alpha3' => 'COK',
                'langEN' => 'Cook Islands',
                'langIT' => 'Isole Cook',
                'nazionalitaEN' => 'Cook Island',
                'nazionalitaIT' => 'Isola di Cook',
            ),
            44 => 
            array (
                'alpha2' => 'CL',
                'alpha3' => 'CHL',
                'langEN' => 'Chile',
                'langIT' => 'Cile',
                'nazionalitaEN' => 'Chilean',
                'nazionalitaIT' => 'Cileno',
            ),
            45 => 
            array (
                'alpha2' => 'CM',
                'alpha3' => 'CMR',
                'langEN' => 'Cameroon',
                'langIT' => 'Camerun',
                'nazionalitaEN' => 'Cameroonian',
                'nazionalitaIT' => 'Camerun',
            ),
            46 => 
            array (
                'alpha2' => 'CN',
                'alpha3' => 'CHN',
                'langEN' => 'China',
                'langIT' => 'Cina',
                'nazionalitaEN' => 'Chinese',
                'nazionalitaIT' => 'Cinese',
            ),
            47 => 
            array (
                'alpha2' => 'CO',
                'alpha3' => 'COL',
                'langEN' => 'Colombia',
                'langIT' => 'Colombia',
                'nazionalitaEN' => 'Colombian',
                'nazionalitaIT' => 'Colombiano',
            ),
            48 => 
            array (
                'alpha2' => 'CR',
                'alpha3' => 'CRI',
                'langEN' => 'Costa Rica',
                'langIT' => 'Costa Rica',
                'nazionalitaEN' => 'Costa Rican',
                'nazionalitaIT' => 'Costaricano',
            ),
            49 => 
            array (
                'alpha2' => 'CS',
                'alpha3' => 'SCG',
                'langEN' => 'Serbia and Montenegro',
                'langIT' => 'Serbia e Montenegro',
                'nazionalitaEN' => NULL,
                'nazionalitaIT' => NULL,
            ),
            50 => 
            array (
                'alpha2' => 'CU',
                'alpha3' => 'CUB',
                'langEN' => 'Cuba',
                'langIT' => 'Cuba',
                'nazionalitaEN' => 'Cuban',
                'nazionalitaIT' => 'Cubano',
            ),
            51 => 
            array (
                'alpha2' => 'CV',
                'alpha3' => 'CPV',
                'langEN' => 'Cape Verde',
                'langIT' => 'Capo Verde',
                'nazionalitaEN' => 'Cabo Verdean',
                'nazionalitaIT' => 'Capo Verde',
            ),
            52 => 
            array (
                'alpha2' => 'CX',
                'alpha3' => 'CXR',
                'langEN' => 'Christmas Island',
                'langIT' => 'Isola di Natale',
                'nazionalitaEN' => 'Christmas Island',
                'nazionalitaIT' => 'Isola di Natale',
            ),
            53 => 
            array (
                'alpha2' => 'CY',
                'alpha3' => 'CYP',
                'langEN' => 'Cyprus',
                'langIT' => 'Cipro',
                'nazionalitaEN' => 'Cypriot',
                'nazionalitaIT' => 'Cipriota',
            ),
            54 => 
            array (
                'alpha2' => 'CZ',
                'alpha3' => 'CZE',
                'langEN' => 'Czech Republic',
                'langIT' => 'Repubblica Ceca',
                'nazionalitaEN' => 'Czech',
                'nazionalitaIT' => 'Ceco',
            ),
            55 => 
            array (
                'alpha2' => 'DE',
                'alpha3' => 'DEU',
                'langEN' => 'Germany',
                'langIT' => 'Germania',
                'nazionalitaEN' => 'German',
                'nazionalitaIT' => 'Tedesco',
            ),
            56 => 
            array (
                'alpha2' => 'DJ',
                'alpha3' => 'DJI',
                'langEN' => 'Djibouti',
                'langIT' => 'Gibuti',
                'nazionalitaEN' => 'Djiboutian',
                'nazionalitaIT' => 'Gibuti',
            ),
            57 => 
            array (
                'alpha2' => 'DK',
                'alpha3' => 'DNK',
                'langEN' => 'Denmark',
                'langIT' => 'Danimarca',
                'nazionalitaEN' => 'Danish',
                'nazionalitaIT' => 'Danese',
            ),
            58 => 
            array (
                'alpha2' => 'DM',
                'alpha3' => 'DMA',
                'langEN' => 'Dominica',
                'langIT' => 'Dominica',
                'nazionalitaEN' => 'Dominican',
                'nazionalitaIT' => 'Domenicano',
            ),
            59 => 
            array (
                'alpha2' => 'DO',
                'alpha3' => 'DOM',
                'langEN' => 'Dominican Republic',
                'langIT' => 'Repubblica Dominicana',
                'nazionalitaEN' => 'Dominican',
                'nazionalitaIT' => 'Domenicano',
            ),
            60 => 
            array (
                'alpha2' => 'DZ',
                'alpha3' => 'DZA',
                'langEN' => 'Algeria',
                'langIT' => 'Algeria',
                'nazionalitaEN' => 'Algerian',
                'nazionalitaIT' => 'Algerino',
            ),
            61 => 
            array (
                'alpha2' => 'EC',
                'alpha3' => 'ECU',
                'langEN' => 'Ecuador',
                'langIT' => 'Ecuador',
                'nazionalitaEN' => 'Ecuadorian',
                'nazionalitaIT' => 'Ecuadoriano',
            ),
            62 => 
            array (
                'alpha2' => 'EE',
                'alpha3' => 'EST',
                'langEN' => 'Estonia',
                'langIT' => 'Estonia',
                'nazionalitaEN' => 'Estonian',
                'nazionalitaIT' => 'Estone',
            ),
            63 => 
            array (
                'alpha2' => 'EG',
                'alpha3' => 'EGY',
                'langEN' => 'Egypt',
                'langIT' => 'Egitto',
                'nazionalitaEN' => 'Egyptian',
                'nazionalitaIT' => 'Egiziano',
            ),
            64 => 
            array (
                'alpha2' => 'EH',
                'alpha3' => 'ESH',
                'langEN' => 'Western Sahara',
                'langIT' => 'Sahara Occidentale',
                'nazionalitaEN' => 'Sahrawi, Sahrawian, Sahraouian',
                'nazionalitaIT' => 'Deserto, deserto, deserto',
            ),
            65 => 
            array (
                'alpha2' => 'ER',
                'alpha3' => 'ERI',
                'langEN' => 'Eritrea',
                'langIT' => 'Eritrea',
                'nazionalitaEN' => 'Eritrean',
                'nazionalitaIT' => 'Eritreo',
            ),
            66 => 
            array (
                'alpha2' => 'ES',
                'alpha3' => 'ESP',
                'langEN' => 'Spain',
                'langIT' => 'Spagna',
                'nazionalitaEN' => 'Spanish',
                'nazionalitaIT' => 'Spagnolo',
            ),
            67 => 
            array (
                'alpha2' => 'ET',
                'alpha3' => 'ETH',
                'langEN' => 'Ethiopia',
                'langIT' => 'Etiopia',
                'nazionalitaEN' => 'Ethiopian',
                'nazionalitaIT' => 'Etiope',
            ),
            68 => 
            array (
                'alpha2' => 'FI',
                'alpha3' => 'FIN',
                'langEN' => 'Finland',
                'langIT' => 'Finlandia',
                'nazionalitaEN' => 'Finnish',
                'nazionalitaIT' => 'Finlandese',
            ),
            69 => 
            array (
                'alpha2' => 'FJ',
                'alpha3' => 'FJI',
                'langEN' => 'Fiji',
                'langIT' => 'Fiji',
                'nazionalitaEN' => 'Fijian',
                'nazionalitaIT' => 'Figiano',
            ),
            70 => 
            array (
                'alpha2' => 'FK',
                'alpha3' => 'FLK',
                'langEN' => 'Falkland Islands',
                'langIT' => 'Isole Falkland',
                'nazionalitaEN' => 'Falkland Island',
                'nazionalitaIT' => 'Isole Falkland',
            ),
            71 => 
            array (
                'alpha2' => 'FM',
                'alpha3' => 'FSM',
                'langEN' => 'Federated States of Micronesia',
                'langIT' => 'Stati Federati della Micronesia',
                'nazionalitaEN' => 'Micronesian',
                'nazionalitaIT' => 'Micronesiano',
            ),
            72 => 
            array (
                'alpha2' => 'FO',
                'alpha3' => 'FRO',
                'langEN' => 'Faroe Islands',
                'langIT' => 'Isole Faroe',
                'nazionalitaEN' => 'Faroese',
                'nazionalitaIT' => 'Faroese',
            ),
            73 => 
            array (
                'alpha2' => 'FR',
                'alpha3' => 'FRA',
                'langEN' => 'France',
                'langIT' => 'Francia',
                'nazionalitaEN' => 'French',
                'nazionalitaIT' => 'Francese',
            ),
            74 => 
            array (
                'alpha2' => 'GA',
                'alpha3' => 'GAB',
                'langEN' => 'Gabon',
                'langIT' => 'Gabon',
                'nazionalitaEN' => 'Gabonese',
                'nazionalitaIT' => 'Gabon',
            ),
            75 => 
            array (
                'alpha2' => 'GB',
                'alpha3' => 'GBR',
                'langEN' => 'United Kingdom',
                'langIT' => 'Regno Unito',
                'nazionalitaEN' => 'British, UK',
                'nazionalitaIT' => 'Britannico, Regno Unito',
            ),
            76 => 
            array (
                'alpha2' => 'GD',
                'alpha3' => 'GRD',
                'langEN' => 'Grenada',
                'langIT' => 'Grenada',
                'nazionalitaEN' => 'Grenadian',
                'nazionalitaIT' => 'Grenadian',
            ),
            77 => 
            array (
                'alpha2' => 'GE',
                'alpha3' => 'GEO',
                'langEN' => 'Georgia',
                'langIT' => 'Georgia',
                'nazionalitaEN' => 'Georgian',
                'nazionalitaIT' => 'Georgiano',
            ),
            78 => 
            array (
                'alpha2' => 'GF',
                'alpha3' => 'GUF',
                'langEN' => 'French Guiana',
                'langIT' => 'Guyana Francese',
                'nazionalitaEN' => 'French Guianese',
                'nazionalitaIT' => 'Guianese francese',
            ),
            79 => 
            array (
                'alpha2' => 'GH',
                'alpha3' => 'GHA',
                'langEN' => 'Ghana',
                'langIT' => 'Ghana',
                'nazionalitaEN' => 'Ghanaian',
                'nazionalitaIT' => 'Ghanese',
            ),
            80 => 
            array (
                'alpha2' => 'GI',
                'alpha3' => 'GIB',
                'langEN' => 'Gibraltar',
                'langIT' => 'Gibilterra',
                'nazionalitaEN' => 'Gibraltar',
                'nazionalitaIT' => 'Gibilterra',
            ),
            81 => 
            array (
                'alpha2' => 'GL',
                'alpha3' => 'GRL',
                'langEN' => 'Greenland',
                'langIT' => 'Groenlandia',
                'nazionalitaEN' => 'Greenlandic',
                'nazionalitaIT' => 'Groenlandese',
            ),
            82 => 
            array (
                'alpha2' => 'GM',
                'alpha3' => 'GMB',
                'langEN' => 'Gambia',
                'langIT' => 'Gambia',
                'nazionalitaEN' => 'Gambian',
                'nazionalitaIT' => 'Gambiano',
            ),
            83 => 
            array (
                'alpha2' => 'GN',
                'alpha3' => 'GIN',
                'langEN' => 'Guinea',
                'langIT' => 'Guinea',
                'nazionalitaEN' => 'Guinean',
                'nazionalitaIT' => 'Guineano',
            ),
            84 => 
            array (
                'alpha2' => 'GP',
                'alpha3' => 'GLP',
                'langEN' => 'Guadeloupe',
                'langIT' => 'Guadalupa',
                'nazionalitaEN' => 'Guadeloupe',
                'nazionalitaIT' => 'Guadalupa',
            ),
            85 => 
            array (
                'alpha2' => 'GQ',
                'alpha3' => 'GNQ',
                'langEN' => 'Equatorial Guinea',
                'langIT' => 'Guinea Equatoriale',
                'nazionalitaEN' => 'Equatorial Guinean, Equatoguinean',
                'nazionalitaIT' => 'Guinea Equatoriale, Equatoguineana',
            ),
            86 => 
            array (
                'alpha2' => 'GR',
                'alpha3' => 'GRC',
                'langEN' => 'Greece',
                'langIT' => 'Grecia',
                'nazionalitaEN' => 'Greek, Hellenic',
                'nazionalitaIT' => 'Greco, ellenico',
            ),
            87 => 
            array (
                'alpha2' => 'GS',
                'alpha3' => 'SGS',
                'langEN' => 'South Georgia and the South Sandwich Islands',
                'langIT' => 'Sud Georgia e Isole Sandwich',
                'nazionalitaEN' => 'South Georgia or South Sandwich Islands',
                'nazionalitaIT' => 'Georgia del Sud o Isole Sandwich Meridionali',
            ),
            88 => 
            array (
                'alpha2' => 'GT',
                'alpha3' => 'GTM',
                'langEN' => 'Guatemala',
                'langIT' => 'Guatemala',
                'nazionalitaEN' => 'Guatemalan',
                'nazionalitaIT' => 'Guatemalteco',
            ),
            89 => 
            array (
                'alpha2' => 'GU',
                'alpha3' => 'GUM',
                'langEN' => 'Guam',
                'langIT' => 'Guam',
                'nazionalitaEN' => 'Guamanian, Guambat',
                'nazionalitaIT' => 'Guamanian, Guambat',
            ),
            90 => 
            array (
                'alpha2' => 'GW',
                'alpha3' => 'GNB',
                'langEN' => 'Guinea-Bissau',
                'langIT' => 'Guinea-Bissau',
                'nazionalitaEN' => 'Bissau-Guinean',
                'nazionalitaIT' => 'Guinea-Bissau',
            ),
            91 => 
            array (
                'alpha2' => 'GY',
                'alpha3' => 'GUY',
                'langEN' => 'Guyana',
                'langIT' => 'Guyana',
                'nazionalitaEN' => 'Guyanese',
                'nazionalitaIT' => 'Guyana',
            ),
            92 => 
            array (
                'alpha2' => 'HK',
                'alpha3' => 'HKG',
                'langEN' => 'Hong Kong',
                'langIT' => 'Hong Kong',
                'nazionalitaEN' => 'Hong Kong, Hong Kongese',
                'nazionalitaIT' => 'Hong Kong, Hong Kong',
            ),
            93 => 
            array (
                'alpha2' => 'HM',
                'alpha3' => 'HMD',
                'langEN' => 'Heard Island and McDonald Islands',
                'langIT' => 'Isola Heard e Isole McDonald',
                'nazionalitaEN' => 'Heard Island or McDonald Islands',
                'nazionalitaIT' => 'Isole Heard e McDonald',
            ),
            94 => 
            array (
                'alpha2' => 'HN',
                'alpha3' => 'HND',
                'langEN' => 'Honduras',
                'langIT' => 'Honduras',
                'nazionalitaEN' => 'Honduran',
                'nazionalitaIT' => 'Honduregno',
            ),
            95 => 
            array (
                'alpha2' => 'HR',
                'alpha3' => 'HRV',
                'langEN' => 'Croatia',
                'langIT' => 'Croazia',
                'nazionalitaEN' => 'Croatian',
                'nazionalitaIT' => 'Croato',
            ),
            96 => 
            array (
                'alpha2' => 'HT',
                'alpha3' => 'HTI',
                'langEN' => 'Haiti',
                'langIT' => 'Haiti',
                'nazionalitaEN' => 'Haitian',
                'nazionalitaIT' => 'Haitiano',
            ),
            97 => 
            array (
                'alpha2' => 'HU',
                'alpha3' => 'HUN',
                'langEN' => 'Hungary',
                'langIT' => 'Ungheria',
                'nazionalitaEN' => 'Hungarian, Magyar',
                'nazionalitaIT' => 'Ungherese, magiaro',
            ),
            98 => 
            array (
                'alpha2' => 'ID',
                'alpha3' => 'IDN',
                'langEN' => 'Indonesia',
                'langIT' => 'Indonesia',
                'nazionalitaEN' => 'Indonesian',
                'nazionalitaIT' => 'Indonesiano',
            ),
            99 => 
            array (
                'alpha2' => 'IE',
                'alpha3' => 'IRL',
                'langEN' => 'Ireland',
                'langIT' => 'Eire',
                'nazionalitaEN' => 'Irish',
                'nazionalitaIT' => 'Irlandesi',
            ),
            100 => 
            array (
                'alpha2' => 'IL',
                'alpha3' => 'ISR',
                'langEN' => 'Israel',
                'langIT' => 'Israele',
                'nazionalitaEN' => 'Israeli',
                'nazionalitaIT' => 'Israeliano',
            ),
            101 => 
            array (
                'alpha2' => 'IM',
                'alpha3' => 'IMN',
                'langEN' => 'Isle of Man',
                'langIT' => 'Isola di Man',
                'nazionalitaEN' => 'Manx',
                'nazionalitaIT' => 'Manx',
            ),
            102 => 
            array (
                'alpha2' => 'IN',
                'alpha3' => 'IND',
                'langEN' => 'India',
                'langIT' => 'India',
                'nazionalitaEN' => 'Indian',
                'nazionalitaIT' => 'Indiano',
            ),
            103 => 
            array (
                'alpha2' => 'IO',
                'alpha3' => 'IOT',
                'langEN' => 'British Indian Ocean Territory',
                'langIT' => 'Territori Britannici dell\'Oceano Indiano',
                'nazionalitaEN' => 'BIOT',
                'nazionalitaIT' => 'BIOT',
            ),
            104 => 
            array (
                'alpha2' => 'IQ',
                'alpha3' => 'IRQ',
                'langEN' => 'Iraq',
                'langIT' => 'Iraq',
                'nazionalitaEN' => 'Iraqi',
                'nazionalitaIT' => 'Iracheno',
            ),
            105 => 
            array (
                'alpha2' => 'IR',
                'alpha3' => 'IRN',
                'langEN' => 'Islamic Republic of Iran',
                'langIT' => 'Iran',
                'nazionalitaEN' => 'Iranian, Persian',
                'nazionalitaIT' => 'Iraniano, persiano',
            ),
            106 => 
            array (
                'alpha2' => 'IS',
                'alpha3' => 'ISL',
                'langEN' => 'Iceland',
                'langIT' => 'Islanda',
                'nazionalitaEN' => 'Icelandic',
                'nazionalitaIT' => 'Islandese',
            ),
            107 => 
            array (
                'alpha2' => 'IT',
                'alpha3' => 'ITA',
                'langEN' => 'Italy',
                'langIT' => 'Italia',
                'nazionalitaEN' => 'Italian',
                'nazionalitaIT' => 'Italiano',
            ),
            108 => 
            array (
                'alpha2' => 'JM',
                'alpha3' => 'JAM',
                'langEN' => 'Jamaica',
                'langIT' => 'Giamaica',
                'nazionalitaEN' => 'Jamaican',
                'nazionalitaIT' => 'Giamaicano',
            ),
            109 => 
            array (
                'alpha2' => 'JO',
                'alpha3' => 'JOR',
                'langEN' => 'Jordan',
                'langIT' => 'Giordania',
                'nazionalitaEN' => 'Jordanian',
                'nazionalitaIT' => 'Giordano',
            ),
            110 => 
            array (
                'alpha2' => 'JP',
                'alpha3' => 'JPN',
                'langEN' => 'Japan',
                'langIT' => 'Giappone',
                'nazionalitaEN' => 'Japanese',
                'nazionalitaIT' => 'Giapponese',
            ),
            111 => 
            array (
                'alpha2' => 'KE',
                'alpha3' => 'KEN',
                'langEN' => 'Kenya',
                'langIT' => 'Kenya',
                'nazionalitaEN' => 'Kenyan',
                'nazionalitaIT' => 'Keniota',
            ),
            112 => 
            array (
                'alpha2' => 'KG',
                'alpha3' => 'KGZ',
                'langEN' => 'Kyrgyzstan',
                'langIT' => 'Kirghizistan',
                'nazionalitaEN' => 'Kyrgyzstani, Kyrgyz, Kirgiz, Kirghiz',
                'nazionalitaIT' => 'Kirghizistan, Kirghizistan, Kirghizistan, Kirghiz',
            ),
            113 => 
            array (
                'alpha2' => 'KH',
                'alpha3' => 'KHM',
                'langEN' => 'Cambodia',
                'langIT' => 'Cambogia',
                'nazionalitaEN' => 'Cambodian',
                'nazionalitaIT' => 'Cambogiano',
            ),
            114 => 
            array (
                'alpha2' => 'KI',
                'alpha3' => 'KIR',
                'langEN' => 'Kiribati',
                'langIT' => 'Kiribati',
                'nazionalitaEN' => 'I-Kiribati',
                'nazionalitaIT' => 'Kiribati',
            ),
            115 => 
            array (
                'alpha2' => 'KM',
                'alpha3' => 'COM',
                'langEN' => 'Comoros',
                'langIT' => 'Comore',
                'nazionalitaEN' => 'Comoran, Comorian',
                'nazionalitaIT' => 'Comore, Comore',
            ),
            116 => 
            array (
                'alpha2' => 'KN',
                'alpha3' => 'KNA',
                'langEN' => 'Saint Kitts and Nevis',
                'langIT' => 'Saint Kitts e Nevis',
                'nazionalitaEN' => 'Kittitian or Nevisian',
                'nazionalitaIT' => 'Kittitian o Nevisian',
            ),
            117 => 
            array (
                'alpha2' => 'KP',
                'alpha3' => 'PRK',
                'langEN' => 'Democratic People\'s Republic of Korea',
                'langIT' => 'Corea del Nord',
                'nazionalitaEN' => 'North Korean',
                'nazionalitaIT' => 'Corea del nord',
            ),
            118 => 
            array (
                'alpha2' => 'KR',
                'alpha3' => 'KOR',
                'langEN' => 'Republic of Korea',
                'langIT' => 'Corea del Sud',
                'nazionalitaEN' => 'South Korean',
                'nazionalitaIT' => 'Corea del Sud',
            ),
            119 => 
            array (
                'alpha2' => 'KW',
                'alpha3' => 'KWT',
                'langEN' => 'Kuwait',
                'langIT' => 'Kuwait',
                'nazionalitaEN' => 'Kuwaiti',
                'nazionalitaIT' => 'Kuwaitiano',
            ),
            120 => 
            array (
                'alpha2' => 'KY',
                'alpha3' => 'CYM',
                'langEN' => 'Cayman Islands',
                'langIT' => 'Isole Cayman',
                'nazionalitaEN' => 'Caymanian',
                'nazionalitaIT' => 'Caimano',
            ),
            121 => 
            array (
                'alpha2' => 'KZ',
                'alpha3' => 'KAZ',
                'langEN' => 'Kazakhstan',
                'langIT' => 'Kazakhistan',
                'nazionalitaEN' => 'Kazakhstani, Kazakh',
                'nazionalitaIT' => 'Kazako, kazako',
            ),
            122 => 
            array (
                'alpha2' => 'LA',
                'alpha3' => 'LAO',
                'langEN' => 'Lao People\'s Democratic Republic',
                'langIT' => 'Laos',
                'nazionalitaEN' => 'Lao, Laotian',
                'nazionalitaIT' => 'Lao, laotiano',
            ),
            123 => 
            array (
                'alpha2' => 'LB',
                'alpha3' => 'LBN',
                'langEN' => 'Lebanon',
                'langIT' => 'Libano',
                'nazionalitaEN' => 'Lebanese',
                'nazionalitaIT' => 'Libanese',
            ),
            124 => 
            array (
                'alpha2' => 'LC',
                'alpha3' => 'LCA',
                'langEN' => 'Saint Lucia',
                'langIT' => 'Santa Lucia',
                'nazionalitaEN' => 'Saint Lucian',
                'nazionalitaIT' => 'San Luciano',
            ),
            125 => 
            array (
                'alpha2' => 'LI',
                'alpha3' => 'LIE',
                'langEN' => 'Liechtenstein',
                'langIT' => 'Liechtenstein',
                'nazionalitaEN' => 'Liechtenstein',
                'nazionalitaIT' => 'Liechtenstein',
            ),
            126 => 
            array (
                'alpha2' => 'LK',
                'alpha3' => 'LKA',
                'langEN' => 'Sri Lanka',
                'langIT' => 'Sri Lanka',
                'nazionalitaEN' => 'Sri Lankan',
                'nazionalitaIT' => 'Sri Lanka',
            ),
            127 => 
            array (
                'alpha2' => 'LR',
                'alpha3' => 'LBR',
                'langEN' => 'Liberia',
                'langIT' => 'Liberia',
                'nazionalitaEN' => 'Liberian',
                'nazionalitaIT' => 'Liberiano',
            ),
            128 => 
            array (
                'alpha2' => 'LS',
                'alpha3' => 'LSO',
                'langEN' => 'Lesotho',
                'langIT' => 'Lesotho',
                'nazionalitaEN' => 'Basotho',
                'nazionalitaIT' => 'Basotho',
            ),
            129 => 
            array (
                'alpha2' => 'LT',
                'alpha3' => 'LTU',
                'langEN' => 'Lithuania',
                'langIT' => 'Lituania',
                'nazionalitaEN' => 'Lithuanian',
                'nazionalitaIT' => 'Lituano',
            ),
            130 => 
            array (
                'alpha2' => 'LU',
                'alpha3' => 'LUX',
                'langEN' => 'Luxembourg',
                'langIT' => 'Lussemburgo',
                'nazionalitaEN' => 'Luxembourg, Luxembourgish',
                'nazionalitaIT' => 'Lussemburghese, lussemburghese',
            ),
            131 => 
            array (
                'alpha2' => 'LV',
                'alpha3' => 'LVA',
                'langEN' => 'Latvia',
                'langIT' => 'Lettonia',
                'nazionalitaEN' => 'Latvian',
                'nazionalitaIT' => 'Lettone',
            ),
            132 => 
            array (
                'alpha2' => 'LY',
                'alpha3' => 'LBY',
                'langEN' => 'Libyan Arab Jamahiriya',
                'langIT' => 'Libia',
                'nazionalitaEN' => 'Libyan',
                'nazionalitaIT' => 'Libico',
            ),
            133 => 
            array (
                'alpha2' => 'MA',
                'alpha3' => 'MAR',
                'langEN' => 'Morocco',
                'langIT' => 'Marocco',
                'nazionalitaEN' => 'Moroccan',
                'nazionalitaIT' => 'Marocchino',
            ),
            134 => 
            array (
                'alpha2' => 'MC',
                'alpha3' => 'MCO',
                'langEN' => 'Monaco',
                'langIT' => 'Monaco',
                'nazionalitaEN' => 'Monégasque, Monacan',
                'nazionalitaIT' => 'Monegasco, monaco',
            ),
            135 => 
            array (
                'alpha2' => 'MD',
                'alpha3' => 'MDA',
                'langEN' => 'Republic of Moldova',
                'langIT' => 'Moldavia',
                'nazionalitaEN' => 'Moldovan',
                'nazionalitaIT' => 'Moldavo',
            ),
            136 => 
            array (
                'alpha2' => 'MG',
                'alpha3' => 'MDG',
                'langEN' => 'Madagascar',
                'langIT' => 'Madagascar',
                'nazionalitaEN' => 'Malagasy',
                'nazionalitaIT' => 'Malgascio',
            ),
            137 => 
            array (
                'alpha2' => 'MH',
                'alpha3' => 'MHL',
                'langEN' => 'Marshall Islands',
                'langIT' => 'Isole Marshall',
                'nazionalitaEN' => 'Marshallese',
                'nazionalitaIT' => 'Marshallese',
            ),
            138 => 
            array (
                'alpha2' => 'MK',
                'alpha3' => 'MKD',
                'langEN' => 'The Former Yugoslav Republic of Macedonia',
                'langIT' => 'Macedonia',
                'nazionalitaEN' => 'Macedonian',
                'nazionalitaIT' => 'Macedone',
            ),
            139 => 
            array (
                'alpha2' => 'ML',
                'alpha3' => 'MLI',
                'langEN' => 'Mali',
                'langIT' => 'Mali',
                'nazionalitaEN' => 'Malian, Malinese',
                'nazionalitaIT' => 'Maliano, maliano',
            ),
            140 => 
            array (
                'alpha2' => 'MM',
                'alpha3' => 'MMR',
                'langEN' => 'Myanmar',
                'langIT' => 'Myanmar',
                'nazionalitaEN' => 'Burmese',
                'nazionalitaIT' => 'Birmano',
            ),
            141 => 
            array (
                'alpha2' => 'MN',
                'alpha3' => 'MNG',
                'langEN' => 'Mongolia',
                'langIT' => 'Mongolia',
                'nazionalitaEN' => 'Mongolian',
                'nazionalitaIT' => 'Mongolo',
            ),
            142 => 
            array (
                'alpha2' => 'MO',
                'alpha3' => 'MAC',
                'langEN' => 'Macao',
                'langIT' => 'Macao',
                'nazionalitaEN' => 'Macanese, Chinese',
                'nazionalitaIT' => 'Macanese, Cinese',
            ),
            143 => 
            array (
                'alpha2' => 'MP',
                'alpha3' => 'MNP',
                'langEN' => 'Northern Mariana Islands',
                'langIT' => 'Isole Marianne Settentrionali',
                'nazionalitaEN' => 'Northern Marianan',
                'nazionalitaIT' => 'Marianne Settentrionali',
            ),
            144 => 
            array (
                'alpha2' => 'MQ',
                'alpha3' => 'MTQ',
                'langEN' => 'Martinique',
                'langIT' => 'Martinica',
                'nazionalitaEN' => 'Martiniquais, Martinican',
                'nazionalitaIT' => 'Martiniquais, Martinica',
            ),
            145 => 
            array (
                'alpha2' => 'MR',
                'alpha3' => 'MRT',
                'langEN' => 'Mauritania',
                'langIT' => 'Mauritania',
                'nazionalitaEN' => 'Mauritanian',
                'nazionalitaIT' => 'Mauritano',
            ),
            146 => 
            array (
                'alpha2' => 'MS',
                'alpha3' => 'MSR',
                'langEN' => 'Montserrat',
                'langIT' => 'Montserrat',
                'nazionalitaEN' => 'Montserratian',
                'nazionalitaIT' => 'Montserratiano',
            ),
            147 => 
            array (
                'alpha2' => 'MT',
                'alpha3' => 'MLT',
                'langEN' => 'Malta',
                'langIT' => 'Malta',
                'nazionalitaEN' => 'Maltese',
                'nazionalitaIT' => 'Maltese',
            ),
            148 => 
            array (
                'alpha2' => 'MU',
                'alpha3' => 'MUS',
                'langEN' => 'Mauritius',
                'langIT' => 'Maurizius',
                'nazionalitaEN' => 'Mauritian',
                'nazionalitaIT' => 'Mauriziano',
            ),
            149 => 
            array (
                'alpha2' => 'MV',
                'alpha3' => 'MDV',
                'langEN' => 'Maldives',
                'langIT' => 'Maldive',
                'nazionalitaEN' => 'Maldivian',
                'nazionalitaIT' => 'Maldiviano',
            ),
            150 => 
            array (
                'alpha2' => 'MW',
                'alpha3' => 'MWI',
                'langEN' => 'Malawi',
                'langIT' => 'Malawi',
                'nazionalitaEN' => 'Malawian',
                'nazionalitaIT' => 'Malawi',
            ),
            151 => 
            array (
                'alpha2' => 'MX',
                'alpha3' => 'MEX',
                'langEN' => 'Mexico',
                'langIT' => 'Messico',
                'nazionalitaEN' => 'Mexican',
                'nazionalitaIT' => 'Messicano',
            ),
            152 => 
            array (
                'alpha2' => 'MY',
                'alpha3' => 'MYS',
                'langEN' => 'Malaysia',
                'langIT' => 'Malesia',
                'nazionalitaEN' => 'Malaysian',
                'nazionalitaIT' => 'Malese',
            ),
            153 => 
            array (
                'alpha2' => 'MZ',
                'alpha3' => 'MOZ',
                'langEN' => 'Mozambique',
                'langIT' => 'Mozambico',
                'nazionalitaEN' => 'Mozambican',
                'nazionalitaIT' => 'Mozambicano',
            ),
            154 => 
            array (
                'alpha2' => 'NA',
                'alpha3' => 'NAM',
                'langEN' => 'Namibia',
                'langIT' => 'Namibia',
                'nazionalitaEN' => 'Namibian',
                'nazionalitaIT' => 'Namibiano',
            ),
            155 => 
            array (
                'alpha2' => 'NC',
                'alpha3' => 'NCL',
                'langEN' => 'New Caledonia',
                'langIT' => 'Nuova Caledonia',
                'nazionalitaEN' => 'New Caledonian',
                'nazionalitaIT' => 'Nuova Caledonia',
            ),
            156 => 
            array (
                'alpha2' => 'NE',
                'alpha3' => 'NER',
                'langEN' => 'Niger',
                'langIT' => 'Niger',
                'nazionalitaEN' => 'Nigerien',
                'nazionalitaIT' => 'Nigeria',
            ),
            157 => 
            array (
                'alpha2' => 'NF',
                'alpha3' => 'NFK',
                'langEN' => 'Norfolk Island',
                'langIT' => 'Isola Norfolk',
                'nazionalitaEN' => 'Norfolk Island',
                'nazionalitaIT' => 'Isola Norfolk',
            ),
            158 => 
            array (
                'alpha2' => 'NG',
                'alpha3' => 'NGA',
                'langEN' => 'Nigeria',
                'langIT' => 'Nigeria',
                'nazionalitaEN' => 'Nigerian',
                'nazionalitaIT' => 'Nigeriano',
            ),
            159 => 
            array (
                'alpha2' => 'NI',
                'alpha3' => 'NIC',
                'langEN' => 'Nicaragua',
                'langIT' => 'Nicaragua',
                'nazionalitaEN' => 'Nicaraguan',
                'nazionalitaIT' => 'Nicaragua',
            ),
            160 => 
            array (
                'alpha2' => 'NL',
                'alpha3' => 'NLD',
                'langEN' => 'Netherlands',
                'langIT' => 'Paesi Bassi',
                'nazionalitaEN' => 'Dutch, Netherlandic',
                'nazionalitaIT' => 'Olandese, olandese',
            ),
            161 => 
            array (
                'alpha2' => 'NO',
                'alpha3' => 'NOR',
                'langEN' => 'Norway',
                'langIT' => 'Norvegia',
                'nazionalitaEN' => 'Norwegian',
                'nazionalitaIT' => 'Norvegese',
            ),
            162 => 
            array (
                'alpha2' => 'NP',
                'alpha3' => 'NPL',
                'langEN' => 'Nepal',
                'langIT' => 'Nepal',
                'nazionalitaEN' => 'Nepali, Nepalese',
                'nazionalitaIT' => 'Nepalese, nepalese',
            ),
            163 => 
            array (
                'alpha2' => 'NR',
                'alpha3' => 'NRU',
                'langEN' => 'Nauru',
                'langIT' => 'Nauru',
                'nazionalitaEN' => 'Nauruan',
                'nazionalitaIT' => 'Nauruan',
            ),
            164 => 
            array (
                'alpha2' => 'NU',
                'alpha3' => 'NIU',
                'langEN' => 'Niue',
                'langIT' => 'Niue',
                'nazionalitaEN' => 'Niuean',
                'nazionalitaIT' => 'Niuean',
            ),
            165 => 
            array (
                'alpha2' => 'NZ',
                'alpha3' => 'NZL',
                'langEN' => 'New Zealand',
                'langIT' => 'Nuova Zelanda',
                'nazionalitaEN' => 'New Zealand, NZ',
                'nazionalitaIT' => 'Nuova Zelanda, Nuova Zelanda',
            ),
            166 => 
            array (
                'alpha2' => 'OM',
                'alpha3' => 'OMN',
                'langEN' => 'Oman',
                'langIT' => 'Oman',
                'nazionalitaEN' => 'Omani',
                'nazionalitaIT' => 'Omani',
            ),
            167 => 
            array (
                'alpha2' => 'PA',
                'alpha3' => 'PAN',
                'langEN' => 'Panama',
                'langIT' => 'Panamá',
                'nazionalitaEN' => 'Panamanian',
                'nazionalitaIT' => 'Panamense',
            ),
            168 => 
            array (
                'alpha2' => 'PE',
                'alpha3' => 'PER',
                'langEN' => 'Peru',
                'langIT' => 'Perù',
                'nazionalitaEN' => 'Peruvian',
                'nazionalitaIT' => 'Peruviano',
            ),
            169 => 
            array (
                'alpha2' => 'PF',
                'alpha3' => 'PYF',
                'langEN' => 'French Polynesia',
                'langIT' => 'Polinesia Francese',
                'nazionalitaEN' => 'French Polynesian',
                'nazionalitaIT' => 'Polinesiano francese',
            ),
            170 => 
            array (
                'alpha2' => 'PG',
                'alpha3' => 'PNG',
                'langEN' => 'Papua New Guinea',
                'langIT' => 'Papua Nuova Guinea',
                'nazionalitaEN' => 'Papua New Guinean, Papuan',
                'nazionalitaIT' => 'Papua Nuova Guinea, Papua',
            ),
            171 => 
            array (
                'alpha2' => 'PH',
                'alpha3' => 'PHL',
                'langEN' => 'Philippines',
                'langIT' => 'Filippine',
                'nazionalitaEN' => 'Philippine, Filipino',
                'nazionalitaIT' => 'Filippino, filippino',
            ),
            172 => 
            array (
                'alpha2' => 'PK',
                'alpha3' => 'PAK',
                'langEN' => 'Pakistan',
                'langIT' => 'Pakistan',
                'nazionalitaEN' => 'Pakistani',
                'nazionalitaIT' => 'Pakistano',
            ),
            173 => 
            array (
                'alpha2' => 'PL',
                'alpha3' => 'POL',
                'langEN' => 'Poland',
                'langIT' => 'Polonia',
                'nazionalitaEN' => 'Polish',
                'nazionalitaIT' => 'Polacco',
            ),
            174 => 
            array (
                'alpha2' => 'PM',
                'alpha3' => 'SPM',
                'langEN' => 'Saint-Pierre and Miquelon',
                'langIT' => 'Saint Pierre e Miquelon',
                'nazionalitaEN' => 'Saint-Pierrais or Miquelonnais',
                'nazionalitaIT' => 'Saint-Pierrais o Miquelonnais',
            ),
            175 => 
            array (
                'alpha2' => 'PN',
                'alpha3' => 'PCN',
                'langEN' => 'Pitcairn',
                'langIT' => 'Pitcairn',
                'nazionalitaEN' => 'Pitcairn Island',
                'nazionalitaIT' => 'Isola Pitcairn',
            ),
            176 => 
            array (
                'alpha2' => 'PR',
                'alpha3' => 'PRI',
                'langEN' => 'Puerto Rico',
                'langIT' => 'Porto Rico',
                'nazionalitaEN' => 'Puerto Rican',
                'nazionalitaIT' => 'Portoricano',
            ),
            177 => 
            array (
                'alpha2' => 'PS',
                'alpha3' => 'PSE',
                'langEN' => 'Occupied Palestinian Territory',
                'langIT' => 'Territori Palestinesi Occupati',
                'nazionalitaEN' => 'Palestinian',
                'nazionalitaIT' => 'Palestinese',
            ),
            178 => 
            array (
                'alpha2' => 'PT',
                'alpha3' => 'PRT',
                'langEN' => 'Portugal',
                'langIT' => 'Portogallo',
                'nazionalitaEN' => 'Portuguese',
                'nazionalitaIT' => 'Portoghese',
            ),
            179 => 
            array (
                'alpha2' => 'PW',
                'alpha3' => 'PLW',
                'langEN' => 'Palau',
                'langIT' => 'Palau',
                'nazionalitaEN' => 'Palauan',
                'nazionalitaIT' => 'Palauan',
            ),
            180 => 
            array (
                'alpha2' => 'PY',
                'alpha3' => 'PRY',
                'langEN' => 'Paraguay',
                'langIT' => 'Paraguay',
                'nazionalitaEN' => 'Paraguayan',
                'nazionalitaIT' => 'Paraguaiano',
            ),
            181 => 
            array (
                'alpha2' => 'QA',
                'alpha3' => 'QAT',
                'langEN' => 'Qatar',
                'langIT' => 'Qatar',
                'nazionalitaEN' => 'Qatari',
                'nazionalitaIT' => 'Del Qatar',
            ),
            182 => 
            array (
                'alpha2' => 'RE',
                'alpha3' => 'REU',
                'langEN' => 'Réunion',
                'langIT' => 'Reunion',
                'nazionalitaEN' => 'Réunionese, Réunionnais',
                'nazionalitaIT' => 'Riunione, Riunione',
            ),
            183 => 
            array (
                'alpha2' => 'RO',
                'alpha3' => 'ROU',
                'langEN' => 'Romania',
                'langIT' => 'Romania',
                'nazionalitaEN' => 'Romanian',
                'nazionalitaIT' => 'Rumeno',
            ),
            184 => 
            array (
                'alpha2' => 'RU',
                'alpha3' => 'RUS',
                'langEN' => 'Russian Federation',
                'langIT' => 'Federazione Russa',
                'nazionalitaEN' => 'Russian',
                'nazionalitaIT' => 'Russo',
            ),
            185 => 
            array (
                'alpha2' => 'RW',
                'alpha3' => 'RWA',
                'langEN' => 'Rwanda',
                'langIT' => 'Ruanda',
                'nazionalitaEN' => 'Rwandan',
                'nazionalitaIT' => 'Ruanda',
            ),
            186 => 
            array (
                'alpha2' => 'SA',
                'alpha3' => 'SAU',
                'langEN' => 'Saudi Arabia',
                'langIT' => 'Arabia Saudita',
                'nazionalitaEN' => 'Saudi, Saudi Arabian',
                'nazionalitaIT' => 'Saudita, Arabia Saudita',
            ),
            187 => 
            array (
                'alpha2' => 'SB',
                'alpha3' => 'SLB',
                'langEN' => 'Solomon Islands',
                'langIT' => 'Isole Solomon',
                'nazionalitaEN' => 'Solomon Island',
                'nazionalitaIT' => 'Isole Salomone',
            ),
            188 => 
            array (
                'alpha2' => 'SC',
                'alpha3' => 'SYC',
                'langEN' => 'Seychelles',
                'langIT' => 'Seychelles',
                'nazionalitaEN' => 'Seychellois',
                'nazionalitaIT' => 'Seychelles',
            ),
            189 => 
            array (
                'alpha2' => 'SD',
                'alpha3' => 'SDN',
                'langEN' => 'Sudan',
                'langIT' => 'Sudan',
                'nazionalitaEN' => 'Sudanese',
                'nazionalitaIT' => 'Sudanese',
            ),
            190 => 
            array (
                'alpha2' => 'SE',
                'alpha3' => 'SWE',
                'langEN' => 'Sweden',
                'langIT' => 'Svezia',
                'nazionalitaEN' => 'Swedish',
                'nazionalitaIT' => 'Svedese',
            ),
            191 => 
            array (
                'alpha2' => 'SG',
                'alpha3' => 'SGP',
                'langEN' => 'Singapore',
                'langIT' => 'Singapore',
                'nazionalitaEN' => 'Singaporean',
                'nazionalitaIT' => 'Di Singapore',
            ),
            192 => 
            array (
                'alpha2' => 'SH',
                'alpha3' => 'SHN',
                'langEN' => 'Saint Helena',
                'langIT' => 'Sant\'Elena',
                'nazionalitaEN' => 'Saint Helenian',
                'nazionalitaIT' => 'Sant\'Elena',
            ),
            193 => 
            array (
                'alpha2' => 'SI',
                'alpha3' => 'SVN',
                'langEN' => 'Slovenia',
                'langIT' => 'Slovenia',
                'nazionalitaEN' => 'Slovenian, Slovene',
                'nazionalitaIT' => 'Sloveno, sloveno',
            ),
            194 => 
            array (
                'alpha2' => 'SJ',
                'alpha3' => 'SJM',
                'langEN' => 'Svalbard and Jan Mayen',
                'langIT' => 'Svalbard e Jan Mayen',
                'nazionalitaEN' => 'Svalbard',
                'nazionalitaIT' => 'Svalbard',
            ),
            195 => 
            array (
                'alpha2' => 'SK',
                'alpha3' => 'SVK',
                'langEN' => 'Slovakia',
                'langIT' => 'Slovacchia',
                'nazionalitaEN' => 'Slovak',
                'nazionalitaIT' => 'Slovacco',
            ),
            196 => 
            array (
                'alpha2' => 'SL',
                'alpha3' => 'SLE',
                'langEN' => 'Sierra Leone',
                'langIT' => 'Sierra Leone',
                'nazionalitaEN' => 'Sierra Leonean',
                'nazionalitaIT' => 'Sierra Leone',
            ),
            197 => 
            array (
                'alpha2' => 'SM',
                'alpha3' => 'SMR',
                'langEN' => 'San Marino',
                'langIT' => 'San Marino',
                'nazionalitaEN' => 'Sammarinese',
                'nazionalitaIT' => 'Sammarinese',
            ),
            198 => 
            array (
                'alpha2' => 'SN',
                'alpha3' => 'SEN',
                'langEN' => 'Senegal',
                'langIT' => 'Senegal',
                'nazionalitaEN' => 'Senegalese',
                'nazionalitaIT' => 'Senegalese',
            ),
            199 => 
            array (
                'alpha2' => 'SO',
                'alpha3' => 'SOM',
                'langEN' => 'Somalia',
                'langIT' => 'Somalia',
                'nazionalitaEN' => 'Somali, Somalian',
                'nazionalitaIT' => 'Somalo, somalo',
            ),
            200 => 
            array (
                'alpha2' => 'SR',
                'alpha3' => 'SUR',
                'langEN' => 'Suriname',
                'langIT' => 'Suriname',
                'nazionalitaEN' => 'Surinamese',
                'nazionalitaIT' => 'Suriname',
            ),
            201 => 
            array (
                'alpha2' => 'SS',
                'alpha3' => 'SSD',
                'langEN' => 'South Sudan',
                'langIT' => 'Sudan del Sud',
                'nazionalitaEN' => 'South Sudanese',
                'nazionalitaIT' => 'Sud Sudan',
            ),
            202 => 
            array (
                'alpha2' => 'ST',
                'alpha3' => 'STP',
                'langEN' => 'Sao Tome and Principe',
                'langIT' => 'Sao Tome e Principe',
                'nazionalitaEN' => 'São Toméan',
                'nazionalitaIT' => 'San Tommaso',
            ),
            203 => 
            array (
                'alpha2' => 'SV',
                'alpha3' => 'SLV',
                'langEN' => 'El Salvador',
                'langIT' => 'El Salvador',
                'nazionalitaEN' => 'Salvadoran',
                'nazionalitaIT' => 'Salvadoregno',
            ),
            204 => 
            array (
                'alpha2' => 'SY',
                'alpha3' => 'SYR',
                'langEN' => 'Syrian Arab Republic',
                'langIT' => 'Siria',
                'nazionalitaEN' => 'Syrian',
                'nazionalitaIT' => 'Siriano',
            ),
            205 => 
            array (
                'alpha2' => 'SZ',
                'alpha3' => 'SWZ',
                'langEN' => 'Swaziland',
                'langIT' => 'Swaziland',
                'nazionalitaEN' => 'Swazi',
                'nazionalitaIT' => 'Swazi',
            ),
            206 => 
            array (
                'alpha2' => 'TC',
                'alpha3' => 'TCA',
                'langEN' => 'Turks and Caicos Islands',
                'langIT' => 'Isole Turks e Caicos',
                'nazionalitaEN' => 'Turks and Caicos Island',
                'nazionalitaIT' => 'Isole Turks e Caicos',
            ),
            207 => 
            array (
                'alpha2' => 'TD',
                'alpha3' => 'TCD',
                'langEN' => 'Chad',
                'langIT' => 'Ciad',
                'nazionalitaEN' => 'Chadian',
                'nazionalitaIT' => 'Ciadiano',
            ),
            208 => 
            array (
                'alpha2' => 'TF',
                'alpha3' => 'ATF',
                'langEN' => 'French Southern Territories',
                'langIT' => 'Territori Francesi del Sud',
                'nazionalitaEN' => 'French Southern Territories',
                'nazionalitaIT' => 'Territori della Francia del sud',
            ),
            209 => 
            array (
                'alpha2' => 'TG',
                'alpha3' => 'TGO',
                'langEN' => 'Togo',
                'langIT' => 'Togo',
                'nazionalitaEN' => 'Togolese',
                'nazionalitaIT' => 'Togolese',
            ),
            210 => 
            array (
                'alpha2' => 'TH',
                'alpha3' => 'THA',
                'langEN' => 'Thailand',
                'langIT' => 'Tailandia',
                'nazionalitaEN' => 'Thai',
                'nazionalitaIT' => 'Tailandese',
            ),
            211 => 
            array (
                'alpha2' => 'TJ',
                'alpha3' => 'TJK',
                'langEN' => 'Tajikistan',
                'langIT' => 'Tagikistan',
                'nazionalitaEN' => 'Tajikistani',
                'nazionalitaIT' => 'Tagikistan',
            ),
            212 => 
            array (
                'alpha2' => 'TK',
                'alpha3' => 'TKL',
                'langEN' => 'Tokelau',
                'langIT' => 'Tokelau',
                'nazionalitaEN' => 'Tokelauan',
                'nazionalitaIT' => 'Tokelauan',
            ),
            213 => 
            array (
                'alpha2' => 'TL',
                'alpha3' => 'TLS',
                'langEN' => 'Timor-Leste',
                'langIT' => 'Timor Est',
                'nazionalitaEN' => 'Timorese',
                'nazionalitaIT' => 'Timorese',
            ),
            214 => 
            array (
                'alpha2' => 'TM',
                'alpha3' => 'TKM',
                'langEN' => 'Turkmenistan',
                'langIT' => 'Turkmenistan',
                'nazionalitaEN' => 'Turkmen',
                'nazionalitaIT' => 'Turkmeno',
            ),
            215 => 
            array (
                'alpha2' => 'TN',
                'alpha3' => 'TUN',
                'langEN' => 'Tunisia',
                'langIT' => 'Tunisia',
                'nazionalitaEN' => 'Tunisian',
                'nazionalitaIT' => 'Tunisino',
            ),
            216 => 
            array (
                'alpha2' => 'TO',
                'alpha3' => 'TON',
                'langEN' => 'Tonga',
                'langIT' => 'Tonga',
                'nazionalitaEN' => 'Tongan',
                'nazionalitaIT' => 'Tongano',
            ),
            217 => 
            array (
                'alpha2' => 'TR',
                'alpha3' => 'TUR',
                'langEN' => 'Turkey',
                'langIT' => 'Turchia',
                'nazionalitaEN' => 'Turkish',
                'nazionalitaIT' => 'Turco',
            ),
            218 => 
            array (
                'alpha2' => 'TT',
                'alpha3' => 'TTO',
                'langEN' => 'Trinidad and Tobago',
                'langIT' => 'Trinidad e Tobago',
                'nazionalitaEN' => 'Trinidadian or Tobagonian',
                'nazionalitaIT' => 'Trinidad e Tobagonian',
            ),
            219 => 
            array (
                'alpha2' => 'TV',
                'alpha3' => 'TUV',
                'langEN' => 'Tuvalu',
                'langIT' => 'Tuvalu',
                'nazionalitaEN' => 'Tuvaluan',
                'nazionalitaIT' => 'Tuvaluano',
            ),
            220 => 
            array (
                'alpha2' => 'TW',
                'alpha3' => 'TWN',
                'langEN' => 'Taiwan',
                'langIT' => 'Taiwan',
                'nazionalitaEN' => 'Chinese, Taiwanese',
                'nazionalitaIT' => 'Cinese, taiwanese',
            ),
            221 => 
            array (
                'alpha2' => 'TZ',
                'alpha3' => 'TZA',
                'langEN' => 'United Republic Of Tanzania',
                'langIT' => 'Tanzania',
                'nazionalitaEN' => 'Tanzanian',
                'nazionalitaIT' => 'Tanzaniano',
            ),
            222 => 
            array (
                'alpha2' => 'UA',
                'alpha3' => 'UKR',
                'langEN' => 'Ukraine',
                'langIT' => 'Ucraina',
                'nazionalitaEN' => 'Ukrainian',
                'nazionalitaIT' => 'Ucraino',
            ),
            223 => 
            array (
                'alpha2' => 'UG',
                'alpha3' => 'UGA',
                'langEN' => 'Uganda',
                'langIT' => 'Uganda',
                'nazionalitaEN' => 'Ugandan',
                'nazionalitaIT' => 'Ugandese',
            ),
            224 => 
            array (
                'alpha2' => 'UM',
                'alpha3' => 'UMI',
                'langEN' => 'United States Minor Outlying Islands',
                'langIT' => 'Isole Minori degli Stati Uniti d\'America',
                'nazionalitaEN' => 'American',
                'nazionalitaIT' => 'Americano',
            ),
            225 => 
            array (
                'alpha2' => 'US',
                'alpha3' => 'USA',
                'langEN' => 'United States',
                'langIT' => 'Stati Uniti d\'America',
                'nazionalitaEN' => 'American',
                'nazionalitaIT' => 'Americano',
            ),
            226 => 
            array (
                'alpha2' => 'UY',
                'alpha3' => 'URY',
                'langEN' => 'Uruguay',
                'langIT' => 'Uruguay',
                'nazionalitaEN' => 'Uruguayan',
                'nazionalitaIT' => 'Uruguaiano',
            ),
            227 => 
            array (
                'alpha2' => 'UZ',
                'alpha3' => 'UZB',
                'langEN' => 'Uzbekistan',
                'langIT' => 'Uzbekistan',
                'nazionalitaEN' => 'Uzbekistani, Uzbek',
                'nazionalitaIT' => 'Uzbeko, uzbeko',
            ),
            228 => 
            array (
                'alpha2' => 'VA',
                'alpha3' => 'VAT',
                'langEN' => 'Vatican City State',
                'langIT' => 'Città del Vaticano',
                'nazionalitaEN' => 'Vatican',
                'nazionalitaIT' => 'Vaticano',
            ),
            229 => 
            array (
                'alpha2' => 'VC',
                'alpha3' => 'VCT',
                'langEN' => 'Saint Vincent and the Grenadines',
                'langIT' => 'Saint Vincent e Grenadine',
                'nazionalitaEN' => 'Saint Vincentian, Vincentian',
                'nazionalitaIT' => 'San Vincenzo, Vincenziano',
            ),
            230 => 
            array (
                'alpha2' => 'VE',
                'alpha3' => 'VEN',
                'langEN' => 'Venezuela',
                'langIT' => 'Venezuela',
                'nazionalitaEN' => 'Venezuelan',
                'nazionalitaIT' => 'Venezuelano',
            ),
            231 => 
            array (
                'alpha2' => 'VG',
                'alpha3' => 'VGB',
                'langEN' => 'British Virgin Islands',
                'langIT' => 'Isole Vergini Britanniche',
                'nazionalitaEN' => 'British Virgin Island',
                'nazionalitaIT' => 'Isole Vergini Britanniche',
            ),
            232 => 
            array (
                'alpha2' => 'VI',
                'alpha3' => 'VIR',
                'langEN' => 'U.S. Virgin Islands',
                'langIT' => 'Isole Vergini Americane',
                'nazionalitaEN' => 'U.S. Virgin Island',
                'nazionalitaIT' => 'Isole Vergini americane',
            ),
            233 => 
            array (
                'alpha2' => 'VN',
                'alpha3' => 'VNM',
                'langEN' => 'Vietnam',
                'langIT' => 'Vietnam',
                'nazionalitaEN' => 'Vietnamese',
                'nazionalitaIT' => 'Vietnamita',
            ),
            234 => 
            array (
                'alpha2' => 'VU',
                'alpha3' => 'VUT',
                'langEN' => 'Vanuatu',
                'langIT' => 'Vanuatu',
                'nazionalitaEN' => 'Ni-Vanuatu, Vanuatuan',
                'nazionalitaIT' => 'Ni-Vanuatu, Vanuatuan',
            ),
            235 => 
            array (
                'alpha2' => 'WF',
                'alpha3' => 'WLF',
                'langEN' => 'Wallis and Futuna',
                'langIT' => 'Wallis e Futuna',
                'nazionalitaEN' => 'Wallis and Futuna, Wallisian or Futunan',
                'nazionalitaIT' => 'Wallis e Futuna, Wallisian o Futunan',
            ),
            236 => 
            array (
                'alpha2' => 'WS',
                'alpha3' => 'WSM',
                'langEN' => 'Samoa',
                'langIT' => 'Samoa',
                'nazionalitaEN' => 'Samoan',
                'nazionalitaIT' => 'Samoano',
            ),
            237 => 
            array (
                'alpha2' => 'YE',
                'alpha3' => 'YEM',
                'langEN' => 'Yemen',
                'langIT' => 'Yemen',
                'nazionalitaEN' => 'Yemeni',
                'nazionalitaIT' => 'Yemenita',
            ),
            238 => 
            array (
                'alpha2' => 'YT',
                'alpha3' => 'MYT',
                'langEN' => 'Mayotte',
                'langIT' => 'Mayotte',
                'nazionalitaEN' => 'Mahoran',
                'nazionalitaIT' => 'Pezzi',
            ),
            239 => 
            array (
                'alpha2' => 'ZA',
                'alpha3' => 'ZAF',
                'langEN' => 'South Africa',
                'langIT' => 'Sud Africa',
                'nazionalitaEN' => 'South African',
                'nazionalitaIT' => 'Sudafricano',
            ),
            240 => 
            array (
                'alpha2' => 'ZM',
                'alpha3' => 'ZMB',
                'langEN' => 'Zambia',
                'langIT' => 'Zambia',
                'nazionalitaEN' => 'Zambian',
                'nazionalitaIT' => 'Zambiano',
            ),
            241 => 
            array (
                'alpha2' => 'ZW',
                'alpha3' => 'ZWE',
                'langEN' => 'Zimbabwe',
                'langIT' => 'Zimbabwe',
                'nazionalitaEN' => 'Zimbabwean',
                'nazionalitaIT' => 'Dello Zimbabwe',
            ),
        ));
        
        
    }
}