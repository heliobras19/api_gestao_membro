<?php

namespace App\Http\Controllers;

use App\Models\Membro;
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

    public function consultarPagamento(Request $request, $id) {
        $request->validate(["ano" => "required"]);
        $quotas = Quota::where([
            "membro_id" => $id,
            "ano" => $request->ano
        ])->orderBy('mes')->pluck('mes')->toArray();
        $situacao = [];
        foreach ([1,2,3,4,5,6,7,8,9,10,11,12] as $key => $value) {
            if (in_array($value, $quotas)) {
                $situacao[$value] = "Pago";
            }else {
                $situacao[$value] = "Não pago";
            }
        }
        return $situacao;
    }

    public function membroPagamento($id) {
        $membro = Membro::with('funcoes', 'orgaos')->where('id', $id)->first();
        $request = new Request([
            'ano' => date('Y')
        ]);
        $membro->pagamentos = $this->consultarPagamento($request, $id);
        return $membro;
    }

    public function pesquisar (Request $request) {
        $membro = Membro::whereRaw("concat(nome, bi, numero_membro, email) LIKE '%{$request->keyboard}%'")
                    ->get(['id', 'nome', 'bi', 'numero_membro', 'email']);
        return $membro;
    }
}
