<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnidadProductiva extends Model
{
    use HasFactory;
    protected $table = 'unidades_productivas';

    protected $fillable = [
        'nombre',
        'campo_id',
        'identificador_local',
        'tipo_identificador_id',
        'activo',
        'completo',
        'superficie',
        'habita',
        'municipio_id',
        'paraje_id',
        'agua_humano_fuente_id',
        'agua_humano_en_casa',
        'agua_humano_distancia',
        'agua_animal_fuente_id',
        'agua_animal_distancia',
        'tipo_pasto_predominante_id',
        'tipo_suelo_predominante_id',
        'forrajeras_predominante',
        'latitud',
        'longitud',
        'observaciones',
    ];

    public function campo()
    {
        return $this->belongsTo(Campo::class);
    }

    public function tipoIdentificador()
    {
        return $this->belongsTo(TipoIdentificador::class);
    }

    public function productores()
    {
        return $this->belongsToMany(
            Productor::class,
            'productor_unidad_productiva'
        );
    }

    public function stock()
    {
        return $this->hasMany(StockAnimal::class);
    }

    public function dispositivos()
    {
        return $this->belongsToMany(
            Dispositivo::class,
            'unidad_productiva_dispositivo'
        );
    }

    public function declaraciones()
    {
        return $this->hasMany(DeclaracionStock::class);
    }

    // Relaciones inversas para los catálogos
    public function fuenteAguaHumano()
    {
        return $this->belongsTo(FuenteAgua::
            class, 'agua_humano_fuente_id');
    }
    public function fuenteAguaAnimal()
    {
        return $this->belongsTo(FuenteAgua::
            class, 'agua_animal_fuente_id');
    }
    public function tipoPasto()
    {
        return $this->belongsTo(
            TipoPasto::class,
            'tipo_pasto_predominante_id'
        );
    }
    public function tipoSuelo()
    {
        return $this->belongsTo(
            TipoSuelo::class,
            'tipo_suelo_predominante_id'
        );
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function paraje()
    {
        return $this->belongsTo(Paraje::class);
    }

    public function condicionTenencia()
    {
        return $this->belongsTo(CondicionTenencia::class);
    }

    /**
     * Relación con datos climáticos
     */
    public function datosClimaticos()
    {
        return $this->hasMany(DatoClimaticoCache::class);
    }

    /**
     * Obtiene los datos climáticos más recientes
     */
    public function climaActual()
    {
        return $this->hasOne(DatoClimaticoCache::class)->latestOfMany('fecha_consulta');
    }
}