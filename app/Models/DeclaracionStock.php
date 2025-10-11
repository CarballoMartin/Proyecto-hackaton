<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeclaracionStock extends Model
{
    use HasFactory;

    protected $table = 'declaraciones_stock';

    protected $fillable = [
        'productor_id',
        'periodo_id',
        'unidad_productiva_id',
        'fecha_declaracion',
        'estado',
        'observaciones',
    ];

    /**
     * Get the producer that owns the declaration.
     */
    public function productor()
    {
        return $this->belongsTo(Productor::class);
    }

    /**
     * Get the period for the declaration.
     */
    public function periodo()
    {
        return $this->belongsTo(ConfiguracionActualizacion::class, 'periodo_id');
    }

    /**
     * Get the stock animal records for the declaration.
     */
    public function stockAnimales()
    {
        return $this->hasMany(StockAnimal::class, 'declaracion_id');
    }
}
