<?php

namespace App\Services;

use App\Models\Productor;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ProductorImporter
{
    public array $errores = [];
    public int $importados = 0;

    public function importarDesdeColeccion(Collection $filas): void
    {
        Productor::withoutEvents(function () use ($filas) {
            foreach ($filas as $index => $fila) {
                $rowNumber = $index + 2; // Asumiendo cabecera en fila 1

                try {
                    // --- VALIDACIÓN DE DATOS MÍNIMOS ---
                    $cuil = $fila['cuil'] ?? null;
                    $dni = $fila['dni'] ?? null;
                    $email = $fila['email'] ?? null;
                    $telefono = $fila['telefono'] ?? null;

                    // 1. Validar que exista un identificador único
                    if (empty($cuil) && empty($dni)) {
                        throw new \Exception("La fila no contiene DNI ni CUIL. Se requiere al menos uno.");
                    }

                    // 2. Validar que exista al menos un método de contacto
                    if (empty($email) && empty($telefono)) {
                        throw new \Exception("La fila no contiene email ni teléfono. Se requiere al menos uno.");
                    }

                    // --- MANEJO DE EMAIL Y GENERACIÓN DE TEMPORAL ---
                    $emailParaUsuario = $email;
                    if (empty($emailParaUsuario)) {
                        // Si no hay email, se genera uno temporal y único usando el identificador.
                        $identificadorParaEmail = !empty($cuil) ? $cuil : $dni;
                        $emailParaUsuario = "{$identificadorParaEmail}@productor.temporal";
                    }

                    // --- CREACIÓN DE USUARIO ---
                    $user = User::firstOrCreate(
                        ['email' => $emailParaUsuario],
                        [
                            'name' => $fila['apellido y nombre'],
                            'password' => Hash::make(Str::random(12)),
                            'rol' => 'productor',
                        ]
                    );

                    // TODO: Disparar evento para notificar creación, especialmente si es con email temporal.
                    // event(new ProductorCreated($user, empty($email))); // Se puede pasar un flag

                    // --- CREACIÓN DE PRODUCTOR ---
                    $identificadorBusqueda = !empty($cuil) ? ['cuil' => $cuil] : ['dni' => $dni];

                    $productorData = [
                        'usuario_id' => $user->id,
                        'nombre' => $fila['apellido y nombre'],
                        'dni' => $dni, // Guardamos el DNI independientemente del método de búsqueda
                        'municipio' => $fila['municipio'] ?? null,
                        'paraje' => $fila['paraje'] ?? null,
                        'direccion' => $fila['direccion'] ?? null,
                        'telefono' => $telefono,
                        'activo' => true,
                    ];

                    // Si usamos DNI para buscar, nos aseguramos de que el CUIL también se guarde/actualice.
                    if (!empty($dni) && empty($cuil)) {
                        // Este caso es menos probable, pero lo cubrimos
                    } else {
                        $productorData['cuil'] = $cuil;
                    }

                    $productor = Productor::updateOrCreate($identificadorBusqueda, $productorData);

                    $this->importados++;

                } catch (Throwable $e) {
                    $errorMessage = "Fila {$rowNumber}: " . $e->getMessage();
                    Log::error('Error al importar productor: ' . $errorMessage);
                    $this->errores[] = $errorMessage;
                    continue;
                }
            }
        });
    }
}
