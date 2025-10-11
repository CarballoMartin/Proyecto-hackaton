<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clima extends Model
{
    protected $table = 'clima';

    protected $fillable = [
        'localidad_id',
        'datos_json',
        'fecha_hora_consulta',
    ];

    /**
     * Obtiene el municipio al que pertenecen los datos del clima.
     */
    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'localidad_id');
    }
}
