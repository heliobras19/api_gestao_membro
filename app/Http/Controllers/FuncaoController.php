<?php

namespace App\Http\Controllers;

use App\Models\Funcao;
use App\Services\APIResponse;
use Illuminate\Http\Request;

class FuncaoController extends Controller
{
    public function index () {
        return response()->json(APIResponse::response([Funcao::all()]));
    }


    public function update (Request $request, Funcao $funcao){
        try {
            $funcao->update($request->all());
            return response()->json(APIResponse::response($funcao));
        } catch (\Exception $exception){
            return response()->json(APIResponse::response($exception->getMessage()));
        }
    }

    public function store (Request $request) {
        try {
            $funcao = Funcao::create($request->all());
            return response()->json(APIResponse::response($funcao));
        } catch (\Exception $exception) {
            return response()->json(APIResponse::response($exception->getMessage(), false), 400);
        }
    }

    public function delete (Funcao $funcao) {
        $funcao->delete();
        return response(APIResponse::response(null, true), 204);
    }
}
