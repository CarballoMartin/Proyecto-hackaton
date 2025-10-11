<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricoActualizacion extends Model
{
    public function configuracion()
    {
        return $this->belongsTo(ConfiguracionActualizacion::class, 'configuracion_id');
    }
}
