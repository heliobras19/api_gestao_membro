<?php

namespace App\Http\Controllers;

use App\Models\Localizacao\Bairro;
use App\Models\Localizacao\Comite;
use App\Models\Localizacao\Comuna;
use App\Models\Localizacao\Municipio;
use App\Models\Localizacao\Provincia;
use App\Models\Membro;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index() {
        $totalComites = Comite::get()->count();
        $totalMembros = Membro::get()->count();
        $membrosProvincia = [];
        $membrosProvinciaUltimos3meses = [];
        $i = 0;
        $provincias = Provincia::with('municipios.comunas.bairros.comites')->get();
       // return dd($provincias);
       // return $provincias;
       $comites = Comite::with('bairro.comuna.municipio.provincia')->whereNull('id_pai')->get();
        $membrosProvincia = [];
        $ultimos3Meses = Carbon::now()->subMonths(3);
        foreach ($comites as $comite) {
            $provinciaNome = $comite->bairro->comuna->municipio->provincia->nome_provincia;

            if (!isset($membrosProvincia[$provinciaNome])) {
                $membrosProvincia[$provinciaNome] = 0;
                $membrosProvinciaUltimos3meses[$provinciaNome] = 0;
            }
            $membros = $comite->membrosDoNucleo($comite->id);
            if (!empty($membros)) {
                $membrosProvincia[$provinciaNome] += count($membros);
            }
            foreach ($membros as $membro) {
                if ($membro->created_at->isAfter($ultimos3Meses) ) {
                    $membrosProvinciaUltimos3meses[$provinciaNome] += 1;
                }
            }
        }

        $membrosPorEstrutura = [
            "jura" =>Membro::where('estrutura', 'JURA')->get()->count(),
            "lima" =>Membro::where('estrutura', 'LIMA')->get()->count(),
            "partido" =>Membro::where('estrutura', 'PARTIDO')->get()->count(),
        ];
        $pais = [
            "provincias" => Provincia::get()->count(),
            "municipios" => Municipio::get()->count(),
            "comunas" => Comuna::get()->count(),
            "bairros" => Bairro::get()->count()
        ];
        $comites = [
            "Setorial" => Comite::where('tipo', 1)->get()->count(),
            "zonais" => Comite::where('tipo', 2)->get()->count(),
            "Local" => Comite::where('tipo', 3)->get()->count(),
            "Nucleos" => Comite::where('tipo', 4)->get()->count(),
            "total_comies" => $totalComites,
            "total_membros" => $totalMembros,
            "membros_provincias" => $membrosProvincia,
            "pais" => $pais,
            "membros_por_estrutura" => $membrosPorEstrutura,
            "membros_ultimos_3_meses" => $membrosProvinciaUltimos3meses

        ];
        return  $comites;
    }
}
