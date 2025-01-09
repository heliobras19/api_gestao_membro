<?php

namespace App\Http\Controllers;

use App\Models\TipoQuota;
use Exception;
use Illuminate\Http\Request;

class TipoQuotaController extends Controller
{
    public function index() {
        $tipos_quotas = TipoQuota::where('cod_quota', 1)->get();
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

    public function destroy(TipoQuota $tipoQuota) {
        $tipoQuota->delete();
        return response()->json(null, 204);
    }
}
