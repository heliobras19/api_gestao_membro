<?php

namespace App\Http\Controllers;

use App\Models\BancosAtuorizados;
use Illuminate\Http\Request;

class BancosAtuorizadosController extends Controller
{
    public function index() {
        $bancos = BancosAtuorizados::all();
        return response()->json($bancos);
    }

    public function store(Request $request)  {
        
        try {
            $request->validate([
                'nome_banco' => 'required',
                'nome_conta' => 'required',
                'numero_conta' => 'required|unique:bancos_atuorizados,numero_conta',
                'iban' => 'required|unique:bancos_atuorizados,iban',
            ]); 
            $banco = BancosAtuorizados::create($request->all());
            return response()->json($banco);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage(), 
                'success' => false
            ]);
        }
    }

    public function update(Request $request, $id) {
        try {
            $banco = BancosAtuorizados::findOrFail($id);
            $request->validate([
                'nome_banco' => 'required',
                'nome_conta' => 'required',
                'numero_conta' => 'required|unique:bancos_atuorizados,numero_conta,' . $id,
                'iban' => 'required|unique:bancos_atuorizados,iban,' . $id,
            ]);
            $banco->update($request->all());
            return response()->json([
                'msg' => 'Banco atualizado com sucesso!',
                'banco' => $banco,
                'success' => true,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage(),
                'success' => false,
            ]);
        }
    }

    public function show($id) {
        try {
            $banco = BancosAtuorizados::findOrFail($id);
            return $banco;
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage(),
                'success' => false,
            ]);
        }
    }

    public function destroy($id) {
        try {
            $banco = BancosAtuorizados::findOrFail($id);
            $banco->delete();
            return response()->json([
                'msg' => 'Banco excluído com sucesso!',
                'success' => true,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'msg' => $th->getMessage(),
                'success' => false,
            ]);
        }
    }
}
