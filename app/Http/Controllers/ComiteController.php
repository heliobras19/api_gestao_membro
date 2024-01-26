<?php

namespace App\Http\Controllers;

use App\Models\Localizacao\Bairro;
use App\Models\Localizacao\Comite;
use App\Services\APIResponse;
use Illuminate\Http\Request;

class ComiteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function index (Request $request) {
            if ($request->has('comite_id')){
                $comite = Comite::with('filhos')->where('id', $request->comite_id);
                return response()->json(APIResponse::response($comite));
            }
            return response()->json(APIResponse::response(Comite::all()));
    }

    public function byBairros (Request $request) {
        $comites = Comite::query()->where('bairro_id', $request->get('bairro_id'))->get();
        return response()->json(APIResponse::response($comites));
    }

    public function ola () {
        return "teste";
    }

    public function show(Comite $comite) {
        return response()->json($comite);
    }

    public function create (){
        $bairros = Bairro::all();
        $comites = Comite::where('tipo', '!=', 4)->get();
        return response()->json(APIResponse::response([
            "bairros" => $bairros,
            "comites" => $comites
        ]));
    }

    public function store(Request $request){
        $request->validate([
            'nome_comite' => "required",
            'bairro_id' => "nullable",
            'id_pai' => "nullable",
            'tipo' => "nullable"
        ]);
        try {
            $comite = Comite::create($request->all());
            return response()->json(APIResponse::response($comite));
        }catch (\Exception $exception) {
            return response()->json(APIResponse::response($exception->getMessage()), 500);
        }
    }

    public function update(Request $request, Comite $comite){
        $request->validate([
            'nome_comite' => "required",
            'bairro_id' => "nullable",
            'id_pai' => "nullable",
            'tipo' => "nullable"
        ]);
        try {
            $comite->update($request->all());
            return response()->json(APIResponse::response($comite));
        }catch (\Exception $exception){
            return response()->json(APIResponse::response($exception->getMessage()), 500);
        }
    }

    public function delete(Comite $comite){
        $comite->delete();
        return response(APIResponse::response(null, true), 204);
    }
}
