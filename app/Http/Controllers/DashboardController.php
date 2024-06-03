<?php

namespace App\Http\Controllers;

use App\Models\Localizacao\Comite;
use App\Models\Localizacao\Provincia;
use App\Models\Membro;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index() {
        $totalComites = Comite::all()->count();
        $totalMembros = Membro::all()->count();
        $membrosProvincia = [];
        $i = 0;
        $provincias = Provincia::with('municipios.comunas.bairros.comites')->get();
       // return dd($provincias);
       // return $provincias;
       $comites = Comite::with('bairro.comuna.municipio.provincia')->get();

$comites = Comite::with('bairro.comuna.municipio.provincia')->get();

        $membrosProvincia = [];

        foreach ($comites as $comite) {
            $provinciaNome = $comite->bairro->comuna->municipio->provincia->nome_provincia;

            if (!isset($membrosProvincia[$provinciaNome])) {
                $membrosProvincia[$provinciaNome] = [];
            }

            $membros = $comite->membrosDoNucleo($comite->id);
            foreach ($membros as $membro) {
                $membrosProvincia[$provinciaNome][] = $membro;
            }
        }
         return $membrosProvincia;
        $comites = [
            "Setorial" => Comite::where('tipo', 1)->get()->count(),
            "zonais" => Comite::where('tipo', 2)->get()->count(),
            "Local" => Comite::where('tipo', 3)->get()->count(),
            "Nucleos" => Comite::where('tipo', 4)->get()->count(),
            "total_comies" => $totalComites,
            "total_membros" => $totalMembros,
            "membros_provincias" => $membrosProvincia
        ];
        return  $comites;
    }
}
