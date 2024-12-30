<?php

namespace App\Http\Controllers;

use App\Models\Localizacao\Comite;
use App\Models\Membro;
use App\Models\Quota;
use Illuminate\Http\Request;

class QuotasRelatorio extends Controller
{
    public function devedores(Request $request) {
        try {
            $request->validate([
                "comite_id" => "required",
                "ano" => "required",
                "mes" => "required",
                "regularizados" => "boolean|nullable"
            ]);

            $comite = new Comite();
            $membros = $comite->membrosDoNucleo($request->comite_id);
            $devedores = [];
            $regularizados = [];
            foreach ($membros as $membro) {
                // Verificar se o membro tem uma quota paga no mês e ano especificados
                $temQuotaPaga = Quota::where('membro_id', $membro->id)
                    ->where('ano', $request->ano)
                    ->where('mes', $request->mes)
                    ->exists();

                if (!$temQuotaPaga) {
                    $devedores[] = [
                        'id' => $membro->id,
                        'nome' => $membro->nome, // Adicione os atributos que deseja retornar
                        'email' => $membro->email,
                        'telefone' => $membro->telefone
                    ];
                }

                if ($temQuotaPaga) {
                    $regularizados[] = [
                        'id' => $membro->id,
                        'nome' => $membro->nome, // Adicione os atributos que deseja retornar
                        'email' => $membro->email,
                        'telefone' => $membro->telefone
                    ];
                }
            }
            return  $request->regularizados ? $regularizados : $devedores;
        } catch (\Throwable $th) {
           return response($th->getMessage(), 400);
        }
    }
}
