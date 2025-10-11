<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CondicionTenencia extends Model
{
    public function productores()
    {
        return $this->belongsToMany(
            Productor::class,
            'productor_unidad_productiva'
        );
    }
}
