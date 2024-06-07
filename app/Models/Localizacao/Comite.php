<?php

namespace App\Models\Localizacao;

use App\Models\Membro;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comite extends Model
{
    use HasFactory, Uuids;
    protected $fillable = ['nome_comite', 'bairro_id', 'id_pai', 'tipo'];
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
        static::addGlobalScope('verificar_escopo', function ($query) {
            $user = auth()->user();
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
        return $this->bairro()->with('comuna.municipio.provincia');
    }

    public function comiteSetorial () {
        $caminho = $this->deepFind($this);
        $tamanho  = count($caminho);
        return $caminho[$tamanho-1];
    }

    private function deepFind ($comite) : array {
        if ($comite->id_pai == null)
            return $this->endereco;
        $comite = Comite::where('id', $comite->id_pai);
        $this->endereco[] = $comite;
        return $this->deepFind($comite);
    }
}
