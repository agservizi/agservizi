<?php

namespace App\Http\Controllers;

use App\Models\CategoriaDocumento;
use App\Models\Documento;
use App\Models\IndisponibilitaOccasionale;
use App\Models\Lezione;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Storage;

class TestController extends Controller
{
    public function __invoke()
    {

        abort(404);
    }
}
