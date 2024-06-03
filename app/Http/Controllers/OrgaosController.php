<?php

namespace App\Http\Controllers;

use App\Models\Orgao;
use App\Services\APIResponse;
use Exception;
use Illuminate\Http\Request;

class OrgaosController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function index()
    {
        $orgaos = Orgao::all();
        return response()->json(APIResponse::response($orgaos));
    }

    // Armazena um novo orgão
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nome_orgao' => 'required|unique:orgaos',
                'tipo' => 'required|in:1,2,3,4'
            ]);
            $orgao = Orgao::create($request->all());
            return response()->json(APIResponse::response($orgao), 201);
        } catch (Exception $th) {
            return response()->json(["msg" => $th->getMessage(), "success" => false], 500);
        }
       

       
    }

    public function show(Orgao $orgao) {
        return response()->json($orgao);
    }

    // Atualiza um orgão existente
    public function update(Request $request, Orgao $orgao)
    {
        $request->validate([
            'nome_orgao' => 'required',
            'tipo' => 'required|in:1,2,3,4'
        ]);

        $orgao->update($request->all());
        return response()->json(APIResponse::response($orgao), 200);
    }

    // Deleta um orgão existente
    public function destroy(Orgao $orgao)
    {
        $orgao->delete();
        return response()->json(null, 204);
    }
}
