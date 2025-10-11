<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
//admin
use App\Http\Controllers\Admin\AdminController;
use App\Livewire\Admin\ProductorPanel;
use App\Livewire\Admin\ImportarProductores;
use App\Livewire\Admin\GestionarSolicitudes;
use App\Livewire\Admin\InstitucionPanel;
use App\Http\Controllers\Admin\InstitucionController;
use App\Http\Controllers\Admin\SolicitudController;
use App\Livewire\Admin\ConfiguracionPanel;
use App\Livewire\Admin\ListarProductores;
use App\Http\Controllers\Institucional\DashboardController;
use App\Http\Controllers\Admin\ProductorController;
use App\Http\Controllers\Admin\SettingsController;

//public

use App\Http\Controllers\ContactController;
use App\Http\Controllers\Productor\MapController;
use App\Http\Controllers\Productor\UnidadProductivaController;
use App\Http\Controllers\Productor\ParajeController;
use App\Http\Controllers\NotificationsController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/notifications', [NotificationsController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationsController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationsController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{id}', [NotificationsController::class, 'destroy'])->name('notifications.destroy');
});

// Rutas para Superadmin
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'role:superadmin'])
    ->prefix('superadmin')
    ->name('admin.')
    ->group(function () {
        
        // Panel Principal
        Route::get('/panel', [AdminController::class, 'panel'])->name('panel');
        Route::get('/maqueta-panel', [AdminController::class, 'panelMaqueta'])->name('panel.maqueta');

        // Productores
        Route::get('/productores/panel', [ProductorController::class, 'panel'])->name('productores.panel');
        Route::get('/productores/listado', [ProductorController::class, 'listado'])->name('productores.listado');
        Route::post('/productores', [ProductorController::class, 'store'])->name('productores.store');
        Route::get('/productores/importar', [ProductorController::class, 'formularioImportacion'])->name('productores.importar');

        // Instituciones
        Route::get('/instituciones', [InstitucionController::class, 'panel'])->name('instituciones.panel');
        Route::post('/instituciones', [InstitucionController::class, 'store'])->name('instituciones.store');
        Route::post('/instituciones/{institucion}/validar', [InstitucionController::class, 'validar'])->name('instituciones.validar');
        Route::post('/instituciones/{institucion}/deactivate', [InstitucionController::class, 'deactivate'])->name('instituciones.deactivate');
        Route::post('/instituciones/{institucion}/destroy', [InstitucionController::class, 'destroy'])->name('instituciones.destroy');

        // Solicitudes de Registro de Instituciones
        Route::get('/solicitudes', [SolicitudController::class, 'index'])->name('solicitudes.gestionar');
        Route::post('/solicitudes/{solicitud}/aprobar', [SolicitudController::class, 'approve'])->name('solicitudes.approve');
        Route::post('/solicitudes/{solicitud}/rechazar', [SolicitudController::class, 'reject'])->name('solicitudes.reject');

        // Configuración
        Route::post('/settings', [SettingsController::class, 'store'])->name('settings.store');
});

// Rutas para Institucional
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'role:institucional'])
    ->prefix('institucional')
    ->name('institucional.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'panelMaqueta'])->name('dashboard');
        
        // Rutas de gestión institucional usando Livewire
        Route::get('/participantes', \App\Livewire\Institucional\Participantes\GestionarParticipantes::class)->name('participantes.index');
        Route::get('/reportes', \App\Livewire\Institucional\Reportes::class)->name('reportes.index');
        Route::get('/mapa', \App\Livewire\Institucional\Mapa::class)->name('mapa.index');
        Route::get('/configuracion', \App\Livewire\Institucional\Configuracion::class)->name('configuracion.index');
    });

Route::get('/panel-maqueta', [DashboardController::class, 'panelMaqueta'])->name('panel.maqueta');

// Ruta para el flujo de bienvenida del productor
Route::middleware(['role:productor'])->group(function () {
    Route::get('/productor/bienvenido', function () {
        $productorNombre = Auth::user()->productor->nombre ?? '';
        return view('productor.welcome-onboarding', ['productorNombre' => $productorNombre]);
    })->name('productor.welcome');
});

// Rutas para Productor
Route::middleware(['role:productor', 'productor.setup'])->group(function () {
    
    // Rutas para el mapa de ubicación
    Route::get('/productor/unidades-productivas/ubicar', [MapController::class, 'show'])->name('productor.up.ubicar');
    Route::post('/productor/unidades-productivas/ubicar', [MapController::class, 'store'])->name('productor.up.ubicar.store');

    Route::get('/productor/panel', [\App\Http\Controllers\Productor\ProductorController::class, 'dashboard'])->name('productor.panel');

    // Rutas para el perfil del productor (modal)
    Route::get('/productor/perfil', [\App\Http\Controllers\Productor\ProductorProfileController::class, 'show'])->name('productor.perfil.show');
    Route::post('/productor/perfil', [\App\Http\Controllers\Productor\ProductorProfileController::class, 'update'])->name('productor.perfil.update');

    Route::get('/productor/unidades-productivas', [\App\Http\Controllers\Productor\ProductorController::class, 'unidadesProductivasIndex'])->name('productor.unidades-productivas.index');
    
    // Rutas para la creación de Unidades Productivas (multi-step form)
    Route::get('/productor/unidades-productivas/crear', [UnidadProductivaController::class, 'createStep1'])->name('productor.unidades-productivas.create');
    Route::post('/productor/unidades-productivas/crear/paso-1', [UnidadProductivaController::class, 'storeStep1'])->name('productor.unidades-productivas.store.step1');
    Route::get('/productor/unidades-productivas/crear/paso-2', [UnidadProductivaController::class, 'createStep2'])->name('productor.unidades-productivas.create.step2');
    Route::get('/productor/unidades-productivas/crear/paso-3', [UnidadProductivaController::class, 'createStep3'])->name('productor.unidades-productivas.create.step3');
    Route::post('/productor/unidades-productivas/crear/finalizar', [UnidadProductivaController::class, 'store'])->name('productor.unidades-productivas.store');

    Route::get('/productor/unidades-productivas/{id}', \App\Livewire\Productor\UnidadesProductivas\GestionarUnidadProductiva::class)->name('productor.unidades-productivas.gestionar');

    Route::get('/productor/stock', [\App\Http\Controllers\Productor\ProductorController::class, 'stockIndex'])->name('productor.stock.index');

    Route::get('/productor/estadisticas', [\App\Http\Controllers\Productor\ProductorController::class, 'estadisticas'])->name('productor.estadisticas.index');

    Route::get('/productor/reportes', [\App\Http\Controllers\Productor\ProductorController::class, 'reportes'])->name('productor.reportes.index');
    Route::get('/productor/chacras/{id}/mapa', \App\Livewire\Productor\UnidadesProductivas\MapaChacra::class)->name('productor.chacras.mapa');
    
    // Rutas para Cuaderno de Campo
    Route::prefix('productor/cuaderno')->name('cuaderno.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Productor\CuadernoDeCampoController::class, 'index'])->name('index');
        Route::get('/inicio', [\App\Http\Controllers\Productor\CuadernoDeCampoController::class, 'inicio'])->name('inicio');
        Route::get('/registro', [\App\Http\Controllers\Productor\CuadernoDeCampoController::class, 'registro'])->name('registro');
        Route::get('/historial', [\App\Http\Controllers\Productor\CuadernoDeCampoController::class, 'historial'])->name('historial');
        Route::get('/historial/exportar-pdf', [\App\Http\Controllers\Productor\CuadernoDeCampoController::class, 'exportarHistorialPdf'])->name('historial.exportar-pdf');
        Route::post('/registro', [\App\Http\Controllers\Productor\CuadernoDeCampoController::class, 'store'])->name('store');
    });

    Route::post('/productor/parajes/validar-temporal', [ParajeController::class, 'validarTemporal'])->name('productor.parajes.validar-temporal');
});

// Rutas publicas
Route::get('/registro-institucional', [\App\Http\Controllers\PaginasEstaticasController::class, 'registroInstitucional'])->name('registro.institucional');
Route::post('/solicitud-institucional', [\App\Http\Controllers\SolicitudController::class, 'store'])->name('solicitud.institucional.store');
Route::get('/solicitud-exitosa', function () {
    return view('livewire.guest.solicitud-exitosa');
})->name('solicitud-exitosa');

Route::post('/landing-contact', [\App\Http\Controllers\LandingPageContactController::class, 'store'])->name('landing.contact.store');

Route::get('/cuenca-misiones', function () {
    return view('pages.cuenca-misiones');
})->name('cuenca-misiones');

Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');
