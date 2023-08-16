<?php

namespace App\Enums;

enum RuoliOperatoreEnum: string
{
    case admin = 'admin';
    //case direttore_sanitario = 'direttore_sanitario';
    //case segretario = 'segretario';
    //case medico = 'medico';
    //case infermiere = 'infermiere';

    public function colore()
    {
        return match ($this) {
            self::admin => 'info',
            // self::segretario => 'light-warning',
            // self::direttore_sanitario => 'warning',
            // self::medico => 'success',
            // self::infermiere => 'light-success',
        };
    }

    public function testo()
    {
        return match ($this) {
            self::admin => 'Amministratore',
            // self::segretario => 'Segretario',
            // self::direttore_sanitario => 'Direttore sanitario',
            // self::medico => 'Medico',
            // self::infermiere => 'Infermiere',
        };
    }
}
