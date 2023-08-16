<?php

namespace App\Http\MieClassi;

use App\Models\RegistroLogin;
use Carbon\Carbon;

class PuliziaDatabase
{
    /**
     * @param $mesi
     * @return int Record eliminati
     */
    static public function pulisciRegistroLogin($mesi): int
    {
        $limit = Carbon::now()->subMonths($mesi)->toDateTimeString();
        return RegistroLogin::where('created_at', '<', $limit)->delete();
    }

    /**
     * @param $mesi
     * @return int Record eliminati
     */
    static public function pulisciRegistroLoginFalliti($mesi): int
    {
        $limit = Carbon::now()->subMonths($mesi)->toDateTimeString();
        return RegistroLogin::where('created_at', '<', $limit)->where('riuscito', '=', 0)->delete();
    }

    /**
     * @return int Record eliminati
     */
    static public function pulisciAllegatiOrfani(): int
    {
        $limit = Carbon::now()->subDays(2)->toDateTimeString();
        $records = ImmagineImmobile::where('created_at', '<', $limit)->whereNull('immobile_id')->get();
        foreach ($records as $record) {
            $record->delete();
        }

        return count($records);
    }





}
