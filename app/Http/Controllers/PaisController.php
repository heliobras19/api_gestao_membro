<?php

namespace App\Http\Controllers;

use App\Models\Localizacao\Bairro;
use App\Models\Localizacao\Comuna;
use App\Models\Localizacao\Municipio;
use App\Models\Localizacao\Provincia;
use App\Services\APIResponse;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class PaisController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['importarDadosView', 'importarDados']);
    }

    public function pais (Request $request) {
        if($request->has('provincia_id')){
            $provincia = Provincia::query()->with('municipios.comunas.bairros.comites')->where('id', $request->provincia_id);
            $user = auth()->user();
            if ($user->abragencia == 'PROVINCIAL' && $user->admin == false){
                $provincia->where('id', $user->scope);
            } elseif ($user->abragencia == 'MUNICIPAL'  && $user->admin == false) {
                $municipio_scope = Municipio::with('provincia')->find($user->scope);
                $provincia->where('id', $municipio_scope->provincia->id);
                $provincia->whereHas('municipios', function ($query) use ($user) {
                    $query->where('id', $user->scope);
                });
            }
            return $provincia->get();
        }
        return Provincia::with('municipios.comunas.bairros.comites')->get();
    }

    public function paisSemAbragencia(Request $request) {
        if ($request->has('provincia_id')) {
            return Provincia::query()->with('municipios.comunas.bairros.comites')->where('id', $request->provincia_id);
        }
         return Provincia::with('municipios.comunas.bairros.comites')->get();
    }

    public function bairros (){
        return response()->json(
            APIResponse::response(Bairro::all())
        );
    }

    public function importarDadosView() {
        return view('importar');
    }

    public function importarDados(Request $request) {
        $path = storage_path() . '/app/' . $request->file('input_file')->store('temp');
        $reader = new Xlsx();
        $excel = $reader->load($path);
        $sheet = $excel->getActiveSheet();
        $info = $reader->listWorksheetInfo($path);
        $totalRows = $info[0]['totalRows'];
        $provincia_nome = $sheet->getCell('A2')->getValue();
        $check = Provincia::where('nome_provincia', $provincia_nome)->get();
        if (count($check) > 0) {
            echo "Provincia {$provincia_nome} ja foi importada, <a href='/excel'>importar outra</a>";
            return;
        }
        $provincia = Provincia::create([
            'nome_provincia' => $provincia_nome
        ]);

        $municipios = [];

        for ($row = 2; $row <= $totalRows; $row++) {
            $municipio_nome = $sheet->getCell("B{$row}")->getValue();
            $comuna_nome = $sheet->getCell("C{$row}")->getValue();
            $bairro_nome = $sheet->getCell("E{$row}")->getValue();

            // Verifica se o município já existe
            $municipio = Municipio::firstOrNew(['nome_municipio' => $municipio_nome]);

            // Se o município não existir, cria um novo
            if (!$municipio->exists) {
                $provincia->municipios()->save($municipio);
            }

            // Verifica se a comuna já existe no município atual
            $comuna = $municipio->comunas()->firstOrNew(['nome_comuna' => $comuna_nome]);

            // Se a comuna não existir, cria uma nova
            if (!$comuna->exists) {
                $municipio->comunas()->save($comuna);
            }

            $bairro = $comuna->bairros()->firstOrNew(['nome_bairro' => $bairro_nome]);

            if (!$bairro->exists) {
                $comuna->bairros()->save($bairro);
            }
        }
        echo "Provincia {$provincia_nome} importada com sucesso !!!, <a href='/excel'>importar outra</a>";
        return;
    }

    public function paisStore(Request $request, $estrutura) {
        if ($estrutura == "municipio") {
            $municipio = Municipio::create($request);
            return $municipio;
        }

        if ($estrutura == "comuna") {
            $municipio = Comuna::create($request);
            return $municipio;
        }

        if ($estrutura == "bairro") {
            $bairro = Bairro::create($request);
            return $bairro;
        }
    }
}
