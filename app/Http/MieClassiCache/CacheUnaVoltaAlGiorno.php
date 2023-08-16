<?php

namespace App\Http\MieClassiCache;

use App\Http\MieClassi\PuliziaDatabase;
use App\Models\RegistroLogin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheUnaVoltaAlGiorno
{
    protected static $cacheKey = 'CacheUnaVoltaAlGiorno';


    public static function get()
    {
        if (Cache::has(self::$cacheKey)) {
            return Cache::get(self::$cacheKey);
        } else {
            return self::creaCache();
        }
    }


    public static function forget()
    {
        Cache::forget(self::$cacheKey);

    }

    protected static function creaCache()
    {
        $start = microtime(true);

        //Run script
        self::run();


        Log::debug('Creata cache ' . self::$cacheKey . ' in ' . number_format(microtime(true) - $start, 3));
        Cache::put(self::$cacheKey, Carbon::now(), Carbon::now()->endOfDay());
        if (env('APP_ENV') == 'production') {
            \Artisan::call('backup:run --only-db --disable-notifications');
            \Artisan::call('backup:clean --disable-notifications');
        }

        return true;


    }


    protected static function run()
    {
        PuliziaDatabase::pulisciRegistroLoginFalliti(2);
        PuliziaDatabase::pulisciRegistroLogin(12);

    }


}
