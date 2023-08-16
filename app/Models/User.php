<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\RuoliOperatoreEnum;
use App\Http\MieClassi\FunzioniContatti;
use App\Notifications\PasswordResetNotification;
use App\Notifications\VerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles,FunzioniContatti;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'ultimo_accesso' => 'datetime',
        'extra' => 'array'
    ];
    
    /*
       |--------------------------------------------------------------------------
       | RELAZIONI
       |--------------------------------------------------------------------------
       */


    /*
    |--------------------------------------------------------------------------
    | SCOPE
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | PASSWORD RESET LOCALIZZATO
    |--------------------------------------------------------------------------
    */

    /**
     * Send the password reset notification.
     *
     * @param string $token
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordResetNotification($token));
    }


    /**********************
     * ALTRO
     **********************/
    public function nominativo()
    {
        return $this->nome . ' ' . $this->cognome;
    }

    public function denominazione()
    {
        return $this->nome . ' ' . $this->cognome;
    }

    public function iniziali()
    {
        return $this->nome[0] . $this->cognome[0];
    }

    public function badgeRuolo()
    {
        $stato = RuoliOperatoreEnum::tryFrom($this->ruolo);
        return '<span class="badge badge-' . $stato->colore() . ' fw-bolder me-2">' . $stato->testo() . '</span>';
    }
    public function coloreRuolo()
    {
        $stato = RuoliOperatoreEnum::tryFrom($this->ruolo);
        return $stato->colore();
    }




    /***************************************************
     * Campo extra
     ***************************************************/


    public function setExtra($value)
    {

        $array = $this->extra;
        foreach ($value as $key => $val) {
            $array[$key] = $val;
        }
        $this->attributes['extra'] = json_encode($array);
        $this->save();

    }


    public function getExtra($key = null)
    {
        if ($key !== null && is_array($this->extra)) {
            if (array_key_exists($key, $this->extra)) {
                return $this->extra[$key];
            }
        }
        return null;
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {

        $this->extra = ['invio_email_verifica' => Carbon::now()->format('d/m/Y H:m:s')];
        $this->save();
        $this->notify(new VerifyEmail());
    }

    public function userLevel($small, $user)
    {

        $livelli = ['operatore', 'admin'];

        foreach ($livelli as $livello) {
            if ($user->permissions->where('nome', $livello)->first()) {
                return $this::labelLivelloOperatore($livello, $small);
            }

        }

    }


    public function labelLivelloOperatore($livello, $small = false)
    {
        if ($small) {
            $small = 'fs-8 px-4 py-3';
        } else {
            $small = '';
        }
        switch ($livello) {
            case 'admin':
                return '<span class="badge badge-info fw-bolder ' . $small . '">Admin</span>';


            case 'teamleader':
                return '<span class="badge badge-light-warning fw-bolder ' . $small . '">Team leader</span>';


            case 'operatore':
                return '<span class="badge badge-light-success fw-bolder ' . $small . '">Operatore</span>';


            case 'supervisore':
                return '<span class="badge badge-light-info fw-bolder ' . $small . '">Supervisore</span>';


            case 'sospeso':
                return '<span class="badge badge-danger fw-bolder ' . $small . '">Sospeso</span>';

        }
    }


}
