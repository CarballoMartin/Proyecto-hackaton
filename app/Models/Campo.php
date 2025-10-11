<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campo extends Model
{
    /**
     * Summary of fillable
     * @var array
     */
    protected $fillable = [
        'localidad',
        'latitud',
        'longitud',
        'nomenclatura_catastral',
        'observaciones',
        'activo',
    ];
    /**
     * Relación uno a muchos con UnidadProductiva.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     * @see UnidadProductiva
     * @see Campo::unidadesProductivas()
     *
     * @var UnidadProductiva[] $unidadesProductivas
     * Las unidades productivas (RNSPAs) asociadas a este campo.
     */
    public function unidadesProductivas()
    {
        return $this->hasMany(UnidadProductiva::class);
    }
}
