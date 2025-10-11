<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\InstitucionalParticipante; 

class Institucion extends Model
{
    /*
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'cuit',
        'contacto_email',
        'logo_path',
        'descripcion',
        'email_secundario',
        'telefono',
        'localidad',
        'provincia',
        'validada',
        'eliminada',
    ];

    public function participantes()
    {
        return $this->hasMany(InstitucionalParticipante::class);
    }
}
