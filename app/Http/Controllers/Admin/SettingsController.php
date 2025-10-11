<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionActualizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;

class SettingsController extends Controller
{
    public function store(Request $request)
    {
        Log::debug('SettingsController@store: Inicio del método.');
        Log::debug('Request data', $request->all());

        $validated = $request->validate([
            'frecuencia_dias' => 'required|integer|min:1',
            'activo' => 'required|boolean',
            'password' => 'required|string',
        ]);

        // 1. Validar la contraseña del superadmin
        if (!Hash::check($validated['password'], Auth::user()->password)) {
            Log::warning('SettingsController@store: Falló la validación de contraseña.');
            throw ValidationException::withMessages([
                'password' => ['La contraseña proporcionada es incorrecta.'],
            ]);
        }
        Log::debug('SettingsController@store: Validación de contraseña exitosa.');

        $configuracion = ConfiguracionActualizacion::firstOrCreate([]);
        Log::debug('SettingsController@store: Configuración encontrada o creada.', ['id' => $configuracion->id]);

        $ahora = now();
        $message = '';

        // 2. Implementar la lógica de "staging"
        $cicloActivo = $configuracion->proxima_actualizacion && $configuracion->proxima_actualizacion->isFuture();
        Log::debug('SettingsController@store: Verificando ciclo.', ['cicloActivo' => $cicloActivo, 'proxima_actualizacion' => $configuracion->proxima_actualizacion ? $configuracion->proxima_actualizacion->toDateTimeString() : null]);

        if ($cicloActivo) {
            Log::debug('SettingsController@store: Ciclo activo detectado. Programando cambios.');
            // Hay un ciclo activo, programar los cambios
            $configuracion->proxima_frecuencia_dias = $validated['frecuencia_dias'];
            $configuracion->proxima_activo = $validated['activo'];
            $message = 'Hay un ciclo activo. Los cambios se han programado y se aplicarán cuando finalice el período actual.';
        } else {
            Log::debug('SettingsController@store: No hay ciclo activo. Aplicando cambios inmediatamente.');
            // No hay ciclo activo, aplicar los cambios inmediatamente
            $configuracion->frecuencia_dias = $validated['frecuencia_dias'];
            $configuracion->activo = $validated['activo'];
            
            // Limpiar los cambios programados por si existían
            $configuracion->proxima_frecuencia_dias = null;
            $configuracion->proxima_activo = null;

            if ($validated['activo']) {
                Log::debug('SettingsController@store: Sistema activado. Creando nuevo ciclo.');
                // Si se está activando, iniciar un nuevo ciclo
                $configuracion->ultima_actualizacion = $ahora;
                $configuracion->proxima_actualizacion = $ahora->addDays((int)$validated['frecuencia_dias']);
                $message = 'Configuración guardada y nuevo ciclo de actualización iniciado.';
            } else {
                Log::debug('SettingsController@store: Sistema desactivado.');
                // Si se está desactivando, asegurar que no haya proxima actualizacion
                $configuracion->proxima_actualizacion = null;
                $message = 'Configuración guardada. El sistema de actualización está inactivo.';
            }
        }
        
        $configuracion->superadmin_id = Auth::id();
        $configuracion->fecha_configuracion = $ahora;

        Log::debug('SettingsController@store: Objeto de configuración antes de guardar.', $configuracion->toArray());
        
        $configuracion->save();
        Log::debug('SettingsController@store: Configuración guardada en la base de datos.');

        if ($request->expectsJson()) {
            $proximaActualizacionFormatted = $configuracion->proxima_actualizacion instanceof \DateTimeInterface
                ? date_format($configuracion->proxima_actualizacion, 'd/m/Y')
                : 'Inactivo';
            
            Log::debug('SettingsController@store: Devolviendo respuesta JSON.', ['message' => $message, 'proxima_actualizacion' => $proximaActualizacionFormatted]);

            return response()->json([
                'message' => $message,
                'proxima_actualizacion' => $proximaActualizacionFormatted
            ]);
        }

        Log::debug('SettingsController@store: Redirigiendo hacia atrás.');
        return Redirect::back()->with('flash.banner', $message);
    }
}