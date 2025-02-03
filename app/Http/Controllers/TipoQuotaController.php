<?php

namespace App\Http\Controllers;

use App\Models\TipoQuota;
use Exception;
use Illuminate\Http\Request;

class TipoQuotaController extends Controller
{
    public function index(Request $request) {
         $nao_ordinarias = false;
        if ($request->has('nao_ordinarias') && $request->nao_ordinarias == true) {
            $nao_ordinarias = true;
        }
        $tipos_quotas = $nao_ordinarias ? 
                        TipoQuota::where('cod_quota', '!=', 1)->get() : 
                        TipoQuota::where('cod_quota', 1)->get();
        return response()->json([$tipos_quotas]);
    }

    public function store(Request $request) {
        try {
            $request->validate([
                'tipo_quota' => 'required',
                'montante' => 'required'
            ]);

            $tipo_quota = TipoQuota::create($request->all());
            return response()->json(["success" => true, "data" => $tipo_quota]);
        } catch( Exception $err) {
            return response()->json(["msg" => $err->getMessage()], 400);
        }
    }

    public function show( $id){
        return TipoQuota::findOrFail($id);
    }

    public function update(Request $request, $id) {
        try {
            $request->validate([
                'montante' => 'required'
            ]);
            $tipo = TipoQuota::find($id);
            $tipo->update($request->all());
            return response(["success" => true, "data" => $tipo]);
        } catch (\Throwable $th) {
            return response()->json(["msg" => $th->getMessage()], 400);
        }
    }

    public function destroy($id) {
       try {
            $tipoQuota = TipoQuota::FindOrFail($id);
            $tipoQuota->delete();
            return response()->json(["success" => true], 200);
       } catch (\Throwable $th) {
            return response()->json(["msg" => $th->getMessage()], 400);
       } 
    }
}
