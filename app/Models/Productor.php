<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productor extends Model
{
    use HasFactory;
    /**
     * Summary of fillable
     * @var array
     */
    protected $fillable = [
        'usuario_id',
        'nombre',
        'dni',
        'cuil',
        'municipio',
        'paraje',
        'direccion',
        'telefono',
        'fecha_nacimiento',
        'activo',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function condicionTenencia()
    {
        return $this->belongsTo(CondicionTenencia::class);
    }

    /**
     * Relación muchos a muchos con UnidadProductiva.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     *
     * @see UnidadProductiva
     * @see Productor::unidadesProductivas()
     *
     * @var UnidadProductiva[] $unidadesProductivas
     * Las unidades productivas (RNSPAs) asociadas a este productor.
     */
    public function unidadesProductivas()
    {
        return $this->belongsToMany(
            UnidadProductiva::class,
            'productor_unidad_productiva'
        );
    }
    // La relación con CondicionTenencia ahora es a través de la tabla pivote
    public function condicionesTenencia()
    {
        return $this->belongsToMany(
            CondicionTenencia::class,
            'productor_unidad_productiva'
        );
    }

    /**
     * Get the stock declarations for the producer.
     */
    public function declaraciones()
    {
        return $this->hasMany(DeclaracionStock::class);
    }
}
