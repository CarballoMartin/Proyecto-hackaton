<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAnimal extends Model
{
    use HasFactory;
    protected $table = 'stock_animals';

    protected $fillable = [
        'declaracion_id',
        'unidad_productiva_id',
        'especie_id',
        'categoria_id',
        'raza_id',
        'tipo_registro_id',
        'cantidad',
        'observaciones',
        'motivo_movimiento_id',
        'destino_traslado',
        'fecha_registro',
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
    ];

    /**
     * Get the declaration that this stock record belongs to.
     */
    public function declaracion()
    {
        return $this->belongsTo(DeclaracionStock::class, 'declaracion_id');
    }

    /**
     * Relación uno a muchos con UnidadProductiva.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     * @see UnidadProductiva
     * @see StockAnimal::unidadProductiva()
     *
     * @var UnidadProductiva $unidadProductiva
     * La unidad productiva asociada a este stock animal.
     */
    public function unidadProductiva()
    {
        return $this->belongsTo(UnidadProductiva::class);
    }

    public function especie()
    {
        return $this->belongsTo(Especie::class);
    }

    public function categoria()
    {
        return $this->belongsTo(CategoriaAnimal::class, 'categoria_id');
    }

    public function raza()
    {
        return $this->belongsTo(Raza::class);
    }

    public function tipoRegistro()
    {
        return $this->belongsTo(TipoRegistro::class);
    }

    public function motivo()
    {
        return $this->belongsTo(MotivoMovimiento::class, 'motivo_movimiento_id');
    }

}
