<?php

namespace App\Actions\Productor;

use App\Models\Productor;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Throwable;

class UpdateProductorProfile
{
    /**
     * Validate and update the producer's profile information.
     *
     * @param Productor $productor
     * @param array $data
     * @return Productor
     * @throws ValidationException|Throwable
     */
    public function handle(Productor $productor, array $data): Productor
    {
        // 1. Normalize input data
        $dniDigits = isset($data['dni']) ? preg_replace('/\D+/', '', $data['dni']) : null;
        $cuilDigits = isset($data['cuil']) ? preg_replace('/\D+/', '', $data['cuil']) : null;
        $telefonoDigits = isset($data['telefono']) ? preg_replace('/\D+/', '', $data['telefono']) : null;

        // 2. Perform custom validation logic
        $validator = Validator::make($data, [
            'nombre' => 'required|string|max:255',
            'fecha_nacimiento' => 'nullable|date',
            'municipio' => 'nullable|string|max:255',
            'paraje' => 'nullable|string|max:255',
            'direccion' => 'nullable|string',
            'email' => 'nullable|email',
        ]);

        $validator->after(function ($validator) use ($dniDigits, $cuilDigits, $telefonoDigits, $productor) {
            if (empty($dniDigits) && empty($cuilDigits)) {
                $validator->errors()->add('dni', 'Debe completar DNI o CUIL (al menos uno).');
                $validator->errors()->add('cuil', 'Debe completar DNI o CUIL (al menos uno).');
            }

            if ($dniDigits && (strlen($dniDigits) < 7 || strlen($dniDigits) > 8)) {
                $validator->errors()->add('dni', 'El DNI debe tener entre 7 y 8 dígitos.');
            }

            if ($cuilDigits && strlen($cuilDigits) !== 11) {
                $validator->errors()->add('cuil', 'El CUIL debe contener 11 dígitos.');
            }

            if ($dniDigits && Productor::where('dni', $dniDigits)->where('id', '<>', $productor->id)->exists()) {
                $validator->errors()->add('dni', 'El DNI ya está en uso por otro productor.');
            }

            if ($cuilDigits && Productor::where('cuil', $cuilDigits)->where('id', '<>', $productor->id)->exists()) {
                $validator->errors()->add('cuil', 'El CUIL ya está en uso por otro productor.');
            }

            if ($productor->telefono && empty($telefonoDigits)) {
                $validator->errors()->add('telefono', 'No puede eliminar el teléfono. Si desea cambiarlo, ingrese un nuevo número.');
            }

            if (empty($productor->telefono) && empty($telefonoDigits)) {
                $validator->errors()->add('telefono', 'Debe completar un teléfono de contacto.');
            }

            if ($telefonoDigits && (strlen($telefonoDigits) < 8 || strlen($telefonoDigits) > 13)) {
                $validator->errors()->add('telefono', 'El teléfono debe tener entre 8 y 13 dígitos.');
            }
        })->validate();

        // 3. If validation passes, update the producer
        try {
            $updateData = [
                'nombre' => $data['nombre'],
                'dni' => $dniDigits ?: null,
                'cuil' => $cuilDigits ?: null,
                'fecha_nacimiento' => $data['fecha_nacimiento'] ?: null,
                'municipio' => $data['municipio'] ?: null,
                'paraje' => $data['paraje'] ?: null,
                'direccion' => $data['direccion'] ?: null,
                'telefono' => $telefonoDigits ?: null,
            ];

            $productor->update($updateData);

            // Note: Email is not updated here to avoid changing user login details without verification.

            return $productor->fresh();

        } catch (Throwable $e) {
            Log::error('UpdateProductorProfileAction error: ' . $e->getMessage(), [
                'productor_id' => $productor->id,
                'data' => $data
            ]);
            throw $e;
        }
    }
}
