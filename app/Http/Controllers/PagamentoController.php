<?php

namespace App\Http\Controllers;

use App\Models\Pagamento;
use App\Models\Quota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagamentoController extends Controller
{

     public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function store(Request $request) {
        try {
            $request->validate([
                'tipo' => 'required',
                'valor' => 'required|regex:/^\d+(\.\d{1,2})?$/',
                'ano' => 'required',
                'meses' => 'required|array',
                'meses.*' => 'integer',
                'data_pagamento' => 'required',
                'referencia_pagamento' => 'required',
                'obs' => 'required',
                'metodo_pagamento' => 'required',
                'membro_id' => 'required',
            ]); 

            $meses = $request->meses;

            $data_pagamento = $request->only(
                                'referencia_pagamento', 
                                'obs', 
                                'metodo_pagamento', 
                                'data_pagamento');

            $data_pagamento['processado_por'] = auth()->id();
            DB::beginTransaction();
            $pagamento = Pagamento::create($data_pagamento);
            foreach ($meses as $key => $mes) {
                Quota::create([
                    'tipo' => $request->tipo,
                    'valor' => $request->valor,
                    'ano' => $request->ano,
                    'mes' => $mes,
                    'pagamento_id' => $pagamento->id,
                    'membro_id' => $request->membro_id
                ]);
            }
            DB::commit();
            
            return response()->json(["success" => true]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["success" => false, "msg" => $th->getMessage()], 400);
        }

    }
}
