<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Especie extends Model
{
    use HasFactory;

    protected $fillable = ['nombre'];
    public function razas()
    {
        return $this->hasMany(Raza::class);
    }

    public function categorias()
    {
        return $this->hasMany(CategoriaAnimal::class);
    }

    public function stock()
    {
        return $this->hasMany(StockAnimal::class);
    }

}
