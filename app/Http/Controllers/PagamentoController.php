<?php

namespace App\Http\Controllers;

use App\Models\Membro;
use App\Models\Pagamento;
use App\Models\Quota;
use App\Models\TipoQuota;
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
                //'banco_id' => 'required',
                'valor' => 'nullable|regex:/^\d+(\.\d{1,2})?$/',
                'ano' => 'required',
                'meses' => 'required|array',
                'meses.*' => 'integer',
                'data_pagamento' => 'required',
                //'referencia_pagamento' => 'required',
                'obs' => 'required',
                'metodo_pagamento' => 'required',
                'membro_id' => 'required',
            ]); 

            $meses = $request->meses;

            $data_pagamento = $request->only(
                                'referencia_pagamento', 
                                'obs', 
                                'tipo',
                                'banco_id',
                                'metodo_pagamento', 
                                'data_pagamento');

            $data_pagamento['processado_por'] = auth()->id();
            DB::beginTransaction();
            $pagamento = Pagamento::create($data_pagamento);
            foreach ($meses as $key => $mes) {

                $existe = Quota::where('ano', $request->ano)
                    ->where('mes', $mes)
                    ->where('membro_id', $request->membro_id)
                    ->where('tipo', $request->tipo)
                    ->exists();

                if ($existe) {
                    return response()->json([
                        "success" => false,
                        "msg" => "O mês $mes do ano {$request->ano} já foi pago para este membro."
                    ], 400);
                }
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
        $tipo_quotas = TipoQuota::all();
        $extraordinario_id = 0;
        $doacao_id = 0;
        foreach ($tipo_quotas as $key => $quota) {
            if ($quota->cod_quota == 2) {
                $doacao_id = $quota->id;
            }

            if ($quota->cod_quota == 3) {
                $extraordinario_id = $quota->id;
            }
        }
     
        $membro = Membro::find($id);
        $ordinario_id = $membro->quota_id;
        $quotas_ordinarias = Quota::where([
            "membro_id" => $id,
            "ano" => $request->ano,
            "tipo" => $ordinario_id
        ])->orderBy('mes')->pluck('mes')->toArray();

        $quotas_extraordinarias = Quota::where([
            "membro_id" => $id,
            "ano" => $request->ano,
            "tipo" => $extraordinario_id
        ])->orderBy('mes')->pluck('valor', 'mes');
        $quotas_doacoes = Quota::where([
            "membro_id" => $id,
            "ano" => $request->ano,
            "tipo" => $doacao_id
        ])->orderBy('mes')->pluck('valor', 'mes');
        $situacao_ordinaria = [];
        $situacao_extraordinaria = [];    
        $situacao_doacao = [];
        foreach ([1,2,3,4,5,6,7,8,9,10,11,12] as $key => $mes) {
            if (in_array($mes, $quotas_ordinarias)) {
                 $situacao_ordinaria[$mes] = "Pago";
            }else {
                $situacao_ordinaria[$mes] = "Não pago";
            }

            if (isset($quotas_extraordinarias[$mes])) {
                $situacao_extraordinaria[$mes] = $quotas_extraordinarias[$mes];
            }else {
                $situacao_extraordinaria[$mes] = "-";
            }

            if (isset($quotas_doacoes[$mes])) {
                $situacao_doacao[$mes] = $quotas_doacoes[$mes];
            }else {
                $situacao_doacao[$mes] = "-";
            }
        }
        return response()->json([
            "ordinaria" => $situacao_ordinaria,
            "extraordinaria" => $situacao_extraordinaria,
            "doacao" => $situacao_doacao
        ]);
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
