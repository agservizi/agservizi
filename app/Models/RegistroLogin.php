<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class RegistroLogin extends Model
{
    //
    protected $table = 'registro_login';

    /** Disabilitato updated_at
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = null;


    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->ip = request()->ip();
            $model->user_agent = request()->userAgent();
        });

    }


    public function utente()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function impersonatoDa()
    {
        return $this->hasOne(User::class, 'id', 'impersonato_da_id');
    }

}
