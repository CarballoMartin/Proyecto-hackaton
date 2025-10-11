<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoIdentificador extends Model
{
    protected $table = 'tipos_identificador';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];

    public function unidadesProductivas()
    {
        return $this->hasMany(UnidadProductiva::class);
    }
}