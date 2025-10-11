<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockActual extends Model
{
    use HasFactory;

    protected $table = 'stock_actual';

    protected $fillable = [
        'unidad_productiva_id',
        'especie_id',
        'categoria_id',
        'raza_id',
        'cantidad_actual',
    ];

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
}