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
            $provincia = Provincia::with('municipios.comunas.bairros.comites')->where('id', $request->provincia_id)->get();
            return $provincia;
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
        $check = Provincia::where('nome_provincia', $provincia_nome);
        if ($check) {
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

            // Cria o bairro na comuna atual
            $comuna->bairros()->create([
                'nome_bairro' => $bairro_nome
            ]);
        }
        echo "Provincia {$provincia_nome} ja foi importada, <a href='/excel'>importar outra</a>";
        return;
    }

}
