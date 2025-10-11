<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudVerificacion extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre_institucion',
        'email_contacto',
        'cuit',
        'nombre_solicitante',
        'telefono_contacto',
        'localidad',
        'provincia',
        'estado',
        'mensaje'
    ];
}
