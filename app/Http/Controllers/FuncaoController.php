<?php

namespace App\Http\Controllers;

use App\Models\Funcao;
use App\Services\APIResponse;
use Illuminate\Http\Request;

class FuncaoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index () {
        return response()->json(APIResponse::response([Funcao::all()]));
    }

    public function show (Funcao $funcao) {
        return response()->json($funcao);
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
            $request->validate([
                "nome_funcao" => "required|unique:funcaos"
            ]);
            $funcao = Funcao::create($request->all());
            return response()->json(APIResponse::response($funcao));
        } catch (\Exception $exception) {
            return response()->json(APIResponse::response($exception->getMessage(), false), 400);
        }
    }

    public function destroy ($id) {
        $funcao = Funcao::find($id); 
        $funcao->delete();
        return response(APIResponse::response(null, true), 200);
    }
}
