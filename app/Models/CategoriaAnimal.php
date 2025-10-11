<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaAnimal extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'especie_id'];
    public function especie()
    {
        return $this->belongsTo(Especie::class);
    }

    public function stock()
    {
        return $this->hasMany(StockAnimal::class, 'categoria_id');
    }
}
