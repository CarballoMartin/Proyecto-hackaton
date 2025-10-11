<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

/** Anotacion para Ides
 * @property string $rol
 * @property bool $verificado
 * @property bool $activo
 */

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    //---Propiedades del modelo----

    // Roles de usuario
    public const ROL_SUPERADMIN = 'superadmin';
    public const ROL_INSTITUCIONAL = 'institucional';
    public const ROL_PRODUCTOR = 'productor';

    public const ROLES = [
        self::ROL_SUPERADMIN,
        self::ROL_INSTITUCIONAL,
        self::ROL_PRODUCTOR,
    ];

    protected $fillable = [
        'id',
        'rol',
        'name',
        'email',
        'password',
        'activo',
        'verificado',
    ];


    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];


    protected $appends = [
        'profile_photo_url',
    ];


    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    //---Metodos del modelo----

    // Relaciones
    public function institucionParticipante()
    {
        return $this->hasOne(InstitucionalParticipante::class, 'usuario_id', 'id');
    }

    // Roles
    public function hasRole(string|array $rol): bool
    {
        return in_array($this->rol, (array) $rol);
    }

    public function productor()
    {
        return $this->hasOne(Productor::class, 'usuario_id');
    }
}
