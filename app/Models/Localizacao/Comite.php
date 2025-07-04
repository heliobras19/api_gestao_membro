<?php

namespace App\Models\Localizacao;

use App\Models\Membro;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comite extends Model
{
    use HasFactory, Uuids;
    protected $fillable = ['nome_comite', 'bairro_id', 'id_pai', 'tipo', 'scope', 'is_provincial'];
    protected $appends = ['descTipo', 'descPai'];
    /**
     * @var array
     */
    private $endereco = [];
    public function getDescTipoAttribute () {
        $tipo_desc = [
           1 => "Comité Sectorial",
           2 => "Comité de Zona",
           3 => "Comité Local",
           4 => "Núcleo"
        ];
        return $tipo_desc[$this->tipo];
    }

    public function getDescPaiAttribute () {
        $pai = Comite::find($this->id_pai);
        if ($pai) {
            return $pai->nome_comite;
        } else {
            return null;
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function($query) {
            $user = auth()->user();
            $scope = $user->scope;
            if ($user->abragencia == 'PROVINCIAL') {
                $municipio = Municipio::where('provincia_id', $user->scope)->first();
                $scope = $municipio->id;
                $query->is_provincial = true;
            }
            $query->scope = $scope;
        });

        static::addGlobalScope('abragencia_show', function ($query) {
            $user = auth()->user();
            if ($user->abragencia == 'MUNICIPAL' && $user->admin == false) {
                $query->whereHas('bairro.comuna.municipio', function ($query) use ($user) {
                    $query->where('id', $user->scope);
                });
            }

            if ($user->abragencia == 'PROVINCIAL' && $user->admin == false) {
                $query->whereHas('bairro.comuna.municipio.provincia', function ($query) use ($user) {
                    $query->where('id', $user->scope);
                });
            }
        });
    }

    public function bairro () {
        return $this->belongsTo(Bairro::class, 'bairro_id');
    }

    public function membros () {
        return $this->hasMany(Membro::class, 'comite_id');
    }


    public function membrosDoNucleo($comiteId) {
        $comite = Comite::find($comiteId);
        if ($comite) {
            if ($comite->tipo == 4) {
                return $comite->membros;
            } else {
                $membros = collect();
                $filhos = $comite->filhos();

                foreach ($filhos as $filho) {
                    $membros = $membros->merge($this->membrosDoNucleo($filho->id));
                }

                return $membros;
            }
        }
        return collect();
    }

    public function filhos () {
        return Comite::where('id_pai', $this->id)->get();
    }

    public function arvore () {
        return $this->bairro()->with('comuna.municipio.provincia')->get();
    }

    public function comiteSetorial () {
        $caminho = $this->deepFind($this);
        $tamanho  = count($caminho);
        return $caminho[$tamanho-1];
    }

    private function deepFind ($comite) : array {
        if ($comite->id_pai == null)
            return $this->endereco;
        $comite = Comite::where('id', $comite->id_pai)->first();
        $this->endereco[] = $comite;
        return $this->deepFind($comite);
    }

   /* public function comiteSetorial() {
    // Busca o comitê raiz (comite setorial)
    return $this->findSetorial($this);
    }

    private function findSetorial($comite) {
        // Se não tem pai, este é o comitê setorial
        if ($comite->id_pai == null) {
            return $comite;
        }
        // Continua subindo na hierarquia para encontrar o setorial
        return Comite::where('id', $comite->id_pai)->first()->findSetorial();
    }*/

}
