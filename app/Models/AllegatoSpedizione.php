<?php

namespace App\Models;

use App\Http\Funzioni\FunzioniAllegato;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllegatoSpedizione extends Model
{
    use FunzioniAllegato;

    protected $table = 'spedizioni_allegati';

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::saving(function ($model) {
            $estensione = strtolower(pathinfo($model->filename_originale, PATHINFO_EXTENSION));
            $model->tipo_file = self::tipoFile($estensione);

            $thumbnailGenerationService = new \App\Services\ThumbnailGenerationService();
            $thumbnailPath = $thumbnailGenerationService->generate($model->path_filename, $model->tipo_file, 500, 500);
            $model->thumbnail = $thumbnailPath;

        });

        static::deleting(function ($model) {
            \Storage::delete($model->path_filename);
            \Log::debug('deleting;');
            if ($model->thumbnail) {
                \Storage::delete($model->thumbnail);
            }

        });
    }


    /*
    |--------------------------------------------------------------------------
    | RELAZIONI
    |--------------------------------------------------------------------------
    */


    public static function perBlade($spedizioneId,$cosa)
    {
        $qb = self::where('spedizione_id',$spedizioneId)->where('cosa',$cosa);
        return $qb->get(['id', 'path_filename', 'dimensione_file', 'thumbnail'])->toArray();
    }
}
