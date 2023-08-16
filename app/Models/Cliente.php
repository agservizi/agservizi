<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Cliente extends Model
{
    use Notifiable;

    protected $table = 'users';

    public function nominativo()
    {
        return $this->cognome . ' ' . $this->nome;
    }
}
