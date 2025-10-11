<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionActualizacion extends Model
{
    use HasFactory;

    protected $fillable = [
        'frecuencia_dias',
        'ultima_actualizacion',
        'proxima_actualizacion',
        'activo',
        'superadmin_id',
        'fecha_configuracion',
        
    ];

    protected $casts = [
        'activo' => 'boolean',
        'ultima_actualizacion' => 'date',
        'proxima_actualizacion' => 'date',
        'fecha_configuracion' => 'datetime',
    ];
    public function superadmin()
    {
        return $this->belongsTo(User::class, 'superadmin_id');
    }

    public function historicos()
    {
        return $this->hasMany(HistoricoActualizacion::class, 'configuracion_id');
    }

    public function stock()
    {
        return $this->hasMany(StockAnimal::class, 'periodo_actualizacion_id');
    }

}
