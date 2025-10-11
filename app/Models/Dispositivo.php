<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispositivo extends Model
{
    public function unidadesProductivas()
    {
        return $this->belongsToMany(
            UnidadProductiva::class,
            'unidad_productiva_dispositivo'
        );
    }

}
