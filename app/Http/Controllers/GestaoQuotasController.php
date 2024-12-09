<?php

namespace App\Http\Controllers;

use App\Models\Membro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GestaoQuotasController extends Controller
{
    public function index(Request $request) {
        try {
            $request->validate([
            'id' => 'required'
            ]);
            
            $membro = Membro::with('orgaos', 'funcoes')->find($request->id);
            return response()->json(["membro" => $membro]);
        } catch (\Throwable $th) {
            return response()->json(["success" => false, "msg" => $th->getMessage()], 400);
        }
    }

    public function store(Request $request, $id) {
            try {
                $request->validate([
                'orgaos' => 'nullable|array',
                'funcoes' =>  'nullable|array'
                ]);
                $membro = Membro::find($id);
                DB::beginTransaction();
                if(!empty($request->post('orgaos')))  $membro->orgaos()->sync($request->post('orgaos'));
                if(!empty($request->post('funcoes'))) $membro->funcoes()->sync($request->post('funcoes'));
                DB::commit();
                return response()->json(["success" => true, "membro" => $membro]);
            } catch (\Throwable $th) {
               return response(["success" => false, "msg" => $th->getMessage()]);
            }
            
    }
}
