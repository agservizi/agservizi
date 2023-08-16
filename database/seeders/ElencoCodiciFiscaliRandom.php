<?php

namespace Database\Seeders;

class ElencoCodiciFiscaliRandom
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

    public static function get()
    {
        return self::$codici[rand(0, count(self::$codici) - 1)];
    }

}
