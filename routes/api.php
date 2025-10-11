<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MapController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ParajeController;
use App\Http\Controllers\Api\LogController;
use App\Http\Controllers\Productor\CuadernoDeCampoController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/logs', [LogController::class, 'index']);
    Route::get('/users', [LogController::class, 'getUsers']);

    // Ruta para el filtro del cuaderno de campo
    Route::get('/cuaderno/movimientos-guardados', [CuadernoDeCampoController::class, 'filtrarMovimientosGuardados'])->name('api.cuaderno.movimientos.filtrar');
});

// Rutas para la autenticación sin contraseña
Route::post('/solicitar-codigo', [AuthController::class, 'solicitarCodigo']);
Route::post('/iniciar-sesion', [AuthController::class, 'iniciarSesion']);

// Ruta para obtener las ubicaciones de los campos con sus productores asociados
Route::get('/locations', [MapController::class, 'getLocations']);

// Ruta para obtener los parajes de un municipio
Route::get('/municipios/{municipio}/parajes', [ParajeController::class, 'index'])->middleware('auth:sanctum');

// Ruta para obtener los datos del clima de todos los municipios
Route::get('/clima', [App\Http\Controllers\Api\ClimaController::class, 'index']);

// Ruta para obtener los datos del clima de las UPs de un productor
Route::get('/productor/clima', [App\Http\Controllers\Api\ProductorClimaController::class, 'index'])->middleware('auth:sanctum');