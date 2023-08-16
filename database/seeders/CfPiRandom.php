<?php

namespace Database\Seeders;

class CfPiRandom
{
    protected static $codici = [
        'PKUPHH77D62I567Y',
        'TNCDCM29L67F329Y',
        'TVYRTR47R52Z508Q',
        'DTHMBC35T21L237R',
        'DLFTDW53E65I615E',
        'PXBBLG34H22D267Z',
        'DZHMFV99S16G011F',
        'PYSDPG75H65C677U',
        'DNVQLP88R07F089W',
        'CPNPPC61A22C943O',
        'TDTFLK89P17D906I',
        'CGUTRE97R11E264P',
        'ZZSNTS63E55G303O',
        'SBLFNZ38T08C867D',
        'GRPDLL83C71H147L',
        'CPPSLL32M51I543Q',
        'TPNDGC49S62C526M',
        'FPHWCR71L31I096R',
        'AOAHLO97D46F570R',
        'ZZVSXT43M52C976X',
        'PKVCMB27T55H148O',
        'ZCURCL68A27A116D',
        'RGMHPL72T59I676B',
        'ZBZVMD69S14H143X',
        'VVRQLP74D11F059F',
        'MSZFCG51E30A810T',
    ];

    protected static $partite = [
        '64735740694',
        '50072020253',
        '52166550815',
        '73403860957',
        '10385600258',
        '67395740185',
        '67015630782',
        '63250010913',
        '32713100488',
        '43887690683',
        '55362140372',
        '57736490046',
        '37984010704',
        '71492050142',
        '57993800424',
        '59756040909',
        '24583570619',
        '31332470108',

    ];


    public static function getCodiceFiscale()
    {
        return self::$codici[rand(0, count(self::$codici) - 1)];
    }
    public static function getPartitaIva()
    {
        return self::$partite[rand(0, count(self::$partite) - 1)];
    }

}
