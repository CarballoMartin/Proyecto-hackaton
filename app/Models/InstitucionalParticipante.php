<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class InstitucionalParticipante extends Model
{
    /*
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'usuario_id',
        'institucion_id',
        'rol',
        'activo',
        'cargo',
        'fecha_ingreso',
    ];
    
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function institucion()
    {
        return $this->belongsTo(Institucion::class);
    }
}
