<?php

namespace App\Http\Controllers;

use App\Http\Requests\Membro\StoreRequest;
use App\Models\Localizacao\Comite;
use App\Models\Membro;
use App\Services\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MembroController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function index(Request $request) {
        if ($request->has('comite_id') && !empty($request->comite_id)){
            $comiteId = $request->get('comite_id');

            $comite = Comite::find($comiteId);

            if ($comite) {
                $membrosDoNucleo = $comite->membrosDoNucleo($comiteId);
                return response()->json(APIResponse::response($membrosDoNucleo, true));
            }
            return response()->json(['message' => 'Comitê não encontrado'], 404);
        }
        return Membro::with('nucleo', 'orgaos', 'funcoes')->get();
    }

    private function rules()
    {
        return [
            'estrutura' => 'nullable|string|max:50',
            'foi_militar' => 'nullable',
            'nome' => 'required|string|max:50',
            'email' => 'nullable|email|max:50',
            'telefone' => 'required|string|max:15',
            'sexo' => 'required|string|size:1',
            'data_nascimento' => 'nullable|date',
            'comuna_id' => 'required|exists:comunas,id',
            'bi' => 'required|string',
            'pai' => 'nullable|string',
            'mae' => 'nullable|string',
            'estado_militante' => 'nullable|string', // Verificar os possíveis estados
            'ano_ingresso' => 'nullable|integer',
            'onde_ingressou' => 'nullable|string',
            'numero_membro' => 'nullable|string',
            'cartao_municipe' => 'nullable|string',
            'comite_id' => 'required|exists:comites,id',
            'orgaos' => 'nullable|array',
            'funcoes' =>  'nullable|array'
        ];
    }
    public function store (Request $request) {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $membro = Membro::create($data);
            if(!empty($request->post('linguas'))) $membro->linguas()->createMany($request->post('linguas'));
            if(!empty($request->post('profissoes'))) $membro->profissoes()->createMany($request->post('profissoes'));
            DB::commit();
            $membro->load('orgaos', 'funcoes', 'linguas', 'profissoes');
            return response()->json(APIResponse::response($membro, true));
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(APIResponse::response($exception->getMessage(), false), 500);
        }
    }

    public function show ($membro) {
        return response()->json(Membro::with('nucleo', 'orgaos', 'funcoes')->where('id', $membro)->get());
    }

    public function teste () {
     // $membro = Membro::with(['orgaos', 'nucleo'])->find(1);
       // return response()->json(APIResponse::response($membro, true));
       $comite = Comite::with('arvore')->find('bed43d2b-8fdc-11ee-a6e7-00155d280abc');
       $descricaoTipo = $comite->descTipo;
        return response()->json(APIResponse::response($comite));
    }


    public function update (Request $request, $id) {
        try {
            $membro = Membro::find($id);
            $membro->update($request->all());
            if(!empty($request->input('linguas'))) $membro->linguas()->createMany($request->input('linguas'));
            if(!empty($request->input('profissoes'))) $membro->profissoes()->createMany($request->input('profissoes'));
            return response()->json(APIResponse::response($membro));
        } catch (\Exception $exception) {
            Log::error(APIResponse::response($exception->getMessage()));
            return response()->json(APIResponse::response($exception->getMessage(), false), 500);
        }
    }

    public function destroy ($id) {
        $membro = Membro::find($id);
        $membro->delete();
        return response()->json(APIResponse::response(["elimidao"]));
    }
}
