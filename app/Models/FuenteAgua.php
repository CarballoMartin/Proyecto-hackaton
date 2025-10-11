<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuenteAgua extends Model
{
    public function camposConsumoHumano()
    {
        return $this->hasMany(Campo::class, 'agua_humano_fuente_id');
    }

    public function camposConsumoAnimal()
    {
        return $this->hasMany(Campo::class, 'agua_animal_fuente_id');
    }

}
