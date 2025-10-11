# 📋 DOCUMENTO FASE 2: Gestión Completa de Campos

## 🎯 **OBJETIVO DE LA FASE**
Completar el sistema CRUD (Create, Read, Update, Delete) para la gestión de campos del productor:
- Formularios completos para crear/editar campos
- Modal para ver detalles del campo
- Modal de confirmación para eliminar campos
- Validación avanzada y mensajes de feedback
- Integración con catálogos de datos

---

## 🛠️ **TECNOLOGÍAS UTILIZADAS**

### **Backend:**
- **Laravel 10.x** - Framework PHP principal
- **Livewire 3.x** - Para interfaces dinámicas sin JavaScript
- **Eloquent ORM** - Manejo de base de datos orientado a objetos
- **Laravel Validation** - Validación de formularios
- **Laravel Events** - Comunicación entre componentes

### **Frontend:**
- **Tailwind CSS** - Framework CSS utility-first
- **Blade Templates** - Motor de plantillas de Laravel
- **Alpine.js** - (Incluido con Livewire) Para interactividad
- **Modales dinámicos** - Para confirmaciones y detalles

---

## 📁 **ARCHIVOS CREADOS/MODIFICADOS**

### **1. Componente CrearCampo**
**Archivo:** `app/Livewire/Productor/Campos/CrearCampo.php`

```php
<?php
namespace App\Livewire\Productor\Campos;

use Livewire\Component;
use Livewire\Attributes\Layout; // ← Atributo para definir el layout
use App\Models\Productor;
use App\Models\Campo;
use App\Models\FuenteAgua;
use App\Models\TipoPasto;
use App\Models\TipoSuelo;

#[Layout('layouts.app')] // ← Define que use el layout 'app'
class CrearCampo extends Component
{
    // ← Propiedades públicas que se sincronizan con el formulario
    public $localidad, $latitud, $longitud, $nomenclatura_catastral;
    public $agua_humano_fuente_id, $agua_humano_en_casa = false, $agua_humano_distancia;
    public $agua_animal_fuente_id, $agua_animal_distancia;
    public $tipo_pasto_predominante_id, $tipo_suelo_predominante_id, $forrajeras_predominante = false;
    public $observaciones;
    public $fuentes_agua, $tipos_pasto, $tipos_suelo; // ← Catálogos de datos
    public $productor;

    public function mount()
    {
        // ← Se ejecuta cuando el componente se inicializa
        $user = auth()->user();
        $this->productor = Productor::where('usuario_id', $user->id)->first();
        
        // ← Carga los catálogos de datos para los dropdowns
        $this->fuentes_agua = FuenteAgua::all();
        $this->tipos_pasto = TipoPasto::all();
        $this->tipos_suelo = TipoSuelo::all();
    }

    public function rules()
    {
        // ← Define reglas de validación
        return [
            'localidad' => 'required|string|max:255',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'nomenclatura_catastral' => 'nullable|string|max:255',
            'agua_humano_fuente_id' => 'required|exists:fuente_aguas,id',
            'agua_humano_en_casa' => 'boolean',
            'agua_humano_distancia' => 'nullable|numeric|min:0',
            'agua_animal_fuente_id' => 'required|exists:fuente_aguas,id',
            'agua_animal_distancia' => 'nullable|numeric|min:0',
            'tipo_pasto_predominante_id' => 'required|exists:tipo_pastos,id',
            'tipo_suelo_predominante_id' => 'required|exists:tipo_suelos,id',
            'forrajeras_predominante' => 'boolean',
            'observaciones' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        // ← Define mensajes personalizados de validación
        return [
            'localidad.required' => 'La localidad es obligatoria.',
            'agua_humano_fuente_id.required' => 'Debe seleccionar una fuente de agua para consumo humano.',
            'agua_animal_fuente_id.required' => 'Debe seleccionar una fuente de agua para consumo animal.',
            'tipo_pasto_predominante_id.required' => 'Debe seleccionar el tipo de pasto predominante.',
            'tipo_suelo_predominante_id.required' => 'Debe seleccionar el tipo de suelo predominante.',
        ];
    }

    public function crearCampo()
    {
        // ← Valida los datos del formulario
        $validatedData = $this->validate();
        
        try {
            // ← Crea el campo con los datos validados
            $campo = new Campo($validatedData);
            $campo->productor_id = $this->productor->id;
            $campo->activo = true; // ← Por defecto activo
            $campo->save();
            
            // ← Mensaje de éxito
            session()->flash('message', 'Campo creado exitosamente.');
            
            // ← Redirige a la lista de campos
            return redirect()->route('productor.campos');
            
        } catch (\Exception $e) {
            // ← Mensaje de error si algo falla
            session()->flash('error', 'Error al crear el campo: ' . $e->getMessage());
        }
    }

    public function cancelar()
    {
        // ← Redirige de vuelta a la lista sin guardar
        return redirect()->route('productor.campos');
    }

    public function render()
    {
        return view('livewire.productor.campos.crear-campo');
    }
}
```

**Explicación línea por línea:**
- `public $localidad, $latitud, ...` - Propiedades que se sincronizan con inputs del formulario
- `public $fuentes_agua, $tipos_pasto, $tipos_suelo` - Catálogos para dropdowns
- `public function mount()` - Método que se ejecuta al inicializar el componente
- `$this->validate()` - Ejecuta las reglas de validación definidas
- `new Campo($validatedData)` - Crea nueva instancia del modelo
- `session()->flash('message', '...')` - Guarda mensaje temporal en sesión

### **2. Vista CrearCampo**
**Archivo:** `resources/views/livewire/productor/campos/crear-campo.blade.php`

```blade
<div>
    {{-- ← Encabezado --}}
    <div class="bg-white shadow-sm border-b mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <h1 class="text-3xl font-bold text-gray-900">Crear Nuevo Campo</h1>
                <p class="mt-1 text-sm text-gray-600">Completa la información de tu nuevo campo.</p>
            </div>
        </div>
    </div>

    {{-- ← Formulario --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-xl p-6">
            <form wire:submit="crearCampo">
                {{-- ← Información Básica --}}
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Información Básica</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="localidad" class="block text-sm font-medium text-gray-700">Localidad *</label>
                            <input type="text" wire:model="localidad" id="localidad" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error('localidad') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        {{-- ← Más campos similares... --}}
                    </div>
                </div>

                {{-- ← Botones --}}
                <div class="flex justify-end space-x-4 pt-6 border-t">
                    <button type="button" wire:click="cancelar" 
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        Crear Campo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
```

**Explicación línea por línea:**
- `<form wire:submit="crearCampo">` - Envía formulario al método crearCampo
- `wire:model="localidad"` - Sincroniza input con propiedad del componente
- `@error('localidad')` - Muestra errores de validación
- `class="grid grid-cols-1 md:grid-cols-2"` - Grid responsive de Tailwind

### **3. Componente EditarCampo**
**Archivo:** `app/Livewire/Productor/Campos/EditarCampo.php`

```php
<?php
namespace App\Livewire\Productor\Campos;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Productor;
use App\Models\Campo;
use App\Models\FuenteAgua;
use App\Models\TipoPasto;
use App\Models\TipoSuelo;

#[Layout('layouts.app')]
class EditarCampo extends Component
{
    // ← Propiedades para el formulario
    public $campo_id;
    public $localidad, $latitud, $longitud, $nomenclatura_catastral;
    public $agua_humano_fuente_id, $agua_humano_en_casa = false, $agua_humano_distancia;
    public $agua_animal_fuente_id, $agua_animal_distancia;
    public $tipo_pasto_predominante_id, $tipo_suelo_predominante_id, $forrajeras_predominante = false;
    public $observaciones;
    public $fuentes_agua, $tipos_pasto, $tipos_suelo;
    public $productor, $campo;

    public function mount($id)
    {
        // ← Se ejecuta cuando el componente se inicializa con el ID
        $user = auth()->user();
        $this->productor = Productor::where('usuario_id', $user->id)->first();
        
        // ← Busca el campo y verifica permisos
        $this->campo = Campo::where('id', $id)
                           ->where('productor_id', $this->productor->id)
                           ->first();
        
        if (!$this->campo) {
            // ← Si no encuentra el campo, redirige con error
            session()->flash('error', 'Campo no encontrado o no tienes permisos para editarlo.');
            return redirect()->route('productor.campos');
        }
        
        // ← Carga datos del campo en las propiedades
        $this->campo_id = $this->campo->id;
        $this->localidad = $this->campo->localidad;
        $this->latitud = $this->campo->latitud;
        $this->longitud = $this->campo->longitud;
        $this->nomenclatura_catastral = $this->campo->nomenclatura_catastral;
        $this->agua_humano_fuente_id = $this->campo->agua_humano_fuente_id;
        $this->agua_humano_en_casa = $this->campo->agua_humano_en_casa;
        $this->agua_humano_distancia = $this->campo->agua_humano_distancia;
        $this->agua_animal_fuente_id = $this->campo->agua_animal_fuente_id;
        $this->agua_animal_distancia = $this->campo->agua_animal_distancia;
        $this->tipo_pasto_predominante_id = $this->campo->tipo_pasto_predominante_id;
        $this->tipo_suelo_predominante_id = $this->campo->tipo_suelo_predominante_id;
        $this->forrajeras_predominante = $this->campo->forrajeras_predominante;
        $this->observaciones = $this->campo->observaciones;
        
        // ← Carga catálogos
        $this->fuentes_agua = FuenteAgua::all();
        $this->tipos_pasto = TipoPasto::all();
        $this->tipos_suelo = TipoSuelo::all();
    }

    public function actualizarCampo()
    {
        // ← Valida los datos del formulario
        $validatedData = $this->validate();
        
        try {
            // ← Actualiza el campo con los datos validados
            $this->campo->update($validatedData);
            
            // ← Mensaje de éxito
            session()->flash('message', 'Campo actualizado exitosamente.');
            
            // ← Redirige a la lista de campos
            return redirect()->route('productor.campos');
            
        } catch (\Exception $e) {
            // ← Mensaje de error si algo falla
            session()->flash('error', 'Error al actualizar el campo: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.productor.campos.editar-campo');
    }
}
```

**Explicación línea por línea:**
- `public function mount($id)` - Recibe el ID del campo a editar
- `->where('productor_id', $this->productor->id)` - Verifica que el campo pertenezca al productor
- `$this->campo->update($validatedData)` - Actualiza el registro existente
- `session()->flash('error', '...')` - Mensaje de error si no tiene permisos

### **4. Componente VerCampo (Modal)**
**Archivo:** `app/Livewire/Productor/Campos/VerCampo.php`

```php
<?php
namespace App\Livewire\Productor\Campos;

use Livewire\Component;
use Livewire\Attributes\On; // ← Importa el atributo On
use App\Models\Productor;
use App\Models\Campo;

#[On('verCampo')] // ← Escucha el evento 'verCampo'

class VerCampo extends Component
{
    // ← Propiedades para el modal
    public $campo_id;
    public $campo;
    public $productor;
    public $showModal = false; // ← Controla si el modal está abierto

    // ← Propiedades para mostrar datos
    public $localidad, $latitud, $longitud, $nomenclatura_catastral;
    public $agua_humano_fuente, $agua_humano_en_casa, $agua_humano_distancia;
    public $agua_animal_fuente, $agua_animal_distancia;
    public $tipo_pasto, $tipo_suelo, $forrajeras_predominante;
    public $observaciones, $activo, $created_at, $updated_at;

    public function mount()
    {
        // ← Obtiene el productor asociado al usuario autenticado
        $user = auth()->user();
        $this->productor = Productor::where('usuario_id', $user->id)->first();
    }

    public function verCampo($id)
    {
        // ← Verifica que el campo pertenezca al productor
        $this->campo = Campo::where('id', $id)
                           ->where('productor_id', $this->productor->id)
                           ->with(['fuenteAguaHumano', 'fuenteAguaAnimal', 'tipoPasto', 'tipoSuelo'])
                           ->first();
        
        if ($this->campo) {
            // ← Carga todos los datos del campo
            $this->campo_id = $this->campo->id;
            $this->localidad = $this->campo->localidad;
            $this->latitud = $this->campo->latitud;
            $this->longitud = $this->campo->longitud;
            $this->nomenclatura_catastral = $this->campo->nomenclatura_catastral;
            
            // ← Datos de agua para humanos
            $this->agua_humano_fuente = $this->campo->fuenteAguaHumano ? $this->campo->fuenteAguaHumano->nombre : 'No especificado';
            $this->agua_humano_en_casa = $this->campo->agua_humano_en_casa;
            $this->agua_humano_distancia = $this->campo->agua_humano_distancia;
            
            // ← Datos de agua para animales
            $this->agua_animal_fuente = $this->campo->fuenteAguaAnimal ? $this->campo->fuenteAguaAnimal->nombre : 'No especificado';
            $this->agua_animal_distancia = $this->campo->agua_animal_distancia;
            
            // ← Datos de pasto y suelo
            $this->tipo_pasto = $this->campo->tipoPasto ? $this->campo->tipoPasto->nombre : 'No especificado';
            $this->tipo_suelo = $this->campo->tipoSuelo ? $this->campo->tipoSuelo->nombre : 'No especificado';
            $this->forrajeras_predominante = $this->campo->forrajeras_predominante;
            
            // ← Otros datos
            $this->observaciones = $this->campo->observaciones;
            $this->activo = $this->campo->activo;
            $this->created_at = $this->campo->created_at;
            $this->updated_at = $this->campo->updated_at;
            
            // ← Abre el modal
            $this->showModal = true;
        } else {
            // ← Si no encuentra el campo, muestra error
            session()->flash('error', 'Campo no encontrado o no tienes permisos para verlo.');
        }
    }

    public function cerrarModal()
    {
        // ← Cierra el modal
        $this->showModal = false;
        $this->resetExcept(['productor']); // ← Limpia todas las propiedades excepto productor
    }

    public function render()
    {
        return view('livewire.productor.campos.ver-campo');
    }
}
```

**Explicación línea por línea:**
- `#[On('verCampo')]` - Escucha el evento 'verCampo' de otros componentes
- `->with(['fuenteAguaHumano', ...])` - Carga relaciones para evitar N+1 queries
- `$this->showModal = true` - Controla la visibilidad del modal
- `$this->resetExcept(['productor'])` - Limpia propiedades manteniendo productor

### **5. Componente EliminarCampo (Modal)**
**Archivo:** `app/Livewire/Productor/Campos/EliminarCampo.php`

```php
<?php
namespace App\Livewire\Productor\Campos;

use Livewire\Component;
use Livewire\Attributes\On; // ← Importa el atributo On
use App\Models\Productor;
use App\Models\Campo;

#[On('eliminarCampo')] // ← Escucha el evento 'eliminarCampo'

class EliminarCampo extends Component
{
    // ← Propiedades para el modal
    public $campo_id;
    public $campo;
    public $productor;
    public $showModal = false; // ← Controla si el modal está abierto
    public $nombre_campo; // ← Nombre del campo para mostrar en confirmación

    public function mount()
    {
        // ← Obtiene el productor asociado al usuario autenticado
        $user = auth()->user();
        $this->productor = Productor::where('usuario_id', $user->id)->first();
    }

    public function confirmarEliminacion($id)
    {
        // ← Verifica que el campo pertenezca al productor
        $this->campo = Campo::where('id', $id)
                           ->where('productor_id', $this->productor->id)
                           ->first();
        
        if ($this->campo) {
            // ← Carga datos del campo para mostrar en confirmación
            $this->campo_id = $this->campo->id;
            $this->nombre_campo = $this->campo->localidad ?: 'Campo ID: ' . $this->campo->id;
            
            // ← Abre el modal de confirmación
            $this->showModal = true;
        } else {
            // ← Si no encuentra el campo, muestra error
            session()->flash('error', 'Campo no encontrado o no tienes permisos para eliminarlo.');
        }
    }

    public function eliminarCampo()
    {
        try {
            // ← Verifica nuevamente que el campo pertenezca al productor
            $campo = Campo::where('id', $this->campo_id)
                         ->where('productor_id', $this->productor->id)
                         ->first();
            
            if ($campo) {
                // ← Elimina el campo
                $campo->delete();
                
                // ← Mensaje de éxito
                session()->flash('message', 'Campo eliminado exitosamente.');
                
                // ← Cierra el modal
                $this->cerrarModal();
                
                // ← Emite evento para refrescar la lista
                $this->dispatch('campoEliminado');
                
            } else {
                session()->flash('error', 'Campo no encontrado o no tienes permisos para eliminarlo.');
            }
            
        } catch (\Exception $e) {
            // ← Mensaje de error si algo falla
            session()->flash('error', 'Error al eliminar el campo: ' . $e->getMessage());
        }
    }

    public function cerrarModal()
    {
        // ← Cierra el modal
        $this->showModal = false;
        $this->resetExcept(['productor']); // ← Limpia todas las propiedades excepto productor
    }

    public function render()
    {
        return view('livewire.productor.campos.eliminar-campo');
    }
}
```

**Explicación línea por línea:**
- `public function confirmarEliminacion($id)` - Abre modal de confirmación
- `$campo->delete()` - Elimina el registro de la base de datos
- `$this->dispatch('campoEliminado')` - Emite evento para refrescar lista
- **Doble verificación** - Verifica permisos antes y después de eliminar

### **6. Rutas Actualizadas**
**Archivo:** `routes/web.php`

```php
// ← Rutas para Productor
Route::middleware(['role:productor'])->group(function () {
    // ← Rutas existentes
    Route::get('/productor/dashboard', \App\Livewire\Productor\Dashboard::class)->name('productor.dashboard');
    Route::get('/productor/perfil', \App\Livewire\Productor\Perfil::class)->name('productor.perfil');
    Route::get('/productor/campos', \App\Livewire\Productor\Campos\ListarCampos::class)->name('productor.campos');
    
    // ← Nuevas rutas para gestión de campos
    Route::get('/productor/campos/crear', \App\Livewire\Productor\Campos\CrearCampo::class)->name('productor.campos.crear');
    Route::get('/productor/campos/{id}/editar', \App\Livewire\Productor\Campos\EditarCampo::class)->name('productor.campos.editar');
});
```

**Explicación línea por línea:**
- `Route::get('/productor/campos/crear', ...)` - Ruta para crear campos
- `Route::get('/productor/campos/{id}/editar', ...)` - Ruta con parámetro para editar
- `->name('productor.campos.crear')` - Nombre de ruta para usar en `route()`

### **7. Seeders Creados**
**Archivo:** `database/seeders/FuenteAguaSeeder.php`

```php
<?php
namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FuenteAgua;

class FuenteAguaSeeder extends Seeder
{
    public function run(): void
    {
        // ← Crea fuentes de agua comunes
        FuenteAgua::create(['nombre' => 'Pozo']);
        FuenteAgua::create(['nombre' => 'Arroyo']);
        FuenteAgua::create(['nombre' => 'Río']);
        FuenteAgua::create(['nombre' => 'Laguna']);
        FuenteAgua::create(['nombre' => 'Agua de red']);
        FuenteAgua::create(['nombre' => 'Tanque de agua']);
        FuenteAgua::create(['nombre' => 'Aljibe']);
        FuenteAgua::create(['nombre' => 'Canal']);
        FuenteAgua::create(['nombre' => 'Vertiente']);
        FuenteAgua::create(['nombre' => 'Otro']);
    }
}
```

**Explicación línea por línea:**
- `FuenteAgua::create(['nombre' => 'Pozo'])` - Crea registro en la tabla
- `public function run(): void` - Método que se ejecuta al correr el seeder
- **Datos realistas** - Fuentes de agua comunes en zonas rurales

---

## 🔧 **COMANDOS UTILIZADOS**

```bash
# ← Crear componentes Livewire
php artisan make:livewire Productor/Campos/CrearCampo
php artisan make:livewire Productor/Campos/EditarCampo
php artisan make:livewire Productor/Campos/VerCampo
php artisan make:livewire Productor/Campos/EliminarCampo

# ← Crear seeders
php artisan make:seeder FuenteAguaSeeder
php artisan make:seeder TipoPastoSeeder
php artisan make:seeder TipoSueloSeeder

# ← Ejecutar seeders
php artisan db:seed --class=FuenteAguaSeeder
php artisan db:seed --class=TipoPastoSeeder
php artisan db:seed --class=TipoSueloSeeder

# ← Iniciar servidor
php artisan serve
```

---

## 📊 **RESULTADO DE LA FASE 2**

### **✅ Funcionalidades Completadas:**
1. **Formulario de creación** completo con validación
2. **Formulario de edición** con datos precargados
3. **Modal de detalles** con información completa
4. **Modal de confirmación** para eliminación segura
5. **Catálogos de datos** (fuentes de agua, tipos de pasto/suelo)
6. **Mensajes de feedback** (éxito/error)
7. **Validación de permisos** en todas las operaciones
8. **Eventos Livewire** para comunicación entre componentes

### **🎯 Aprendizajes Clave:**
- **Modales dinámicos** con Livewire para UX mejorada
- **Validación avanzada** con mensajes personalizados
- **Relaciones Eloquent** para evitar N+1 queries
- **Eventos entre componentes** para comunicación
- **Seguridad** con verificación de permisos
- **Catálogos de datos** para consistencia

### **🔒 Características de Seguridad:**
- **Verificación de propiedad** - Solo ve/edita/elimina sus campos
- **Validación de entrada** - Previene datos maliciosos
- **Doble verificación** - En eliminación y edición
- **Manejo de errores** - Try-catch en operaciones críticas

---

## 🔄 **TRANSICIÓN A FASE 3**

La Fase 2 completó el CRUD de campos. Ahora en Fase 3 implementaremos:
- **Gestión de Stock Animal** (CRUD completo)
- **Relaciones con campos** y especies
- **Cálculos automáticos** de totales
- **Reportes básicos** de inventario

---

## 📝 **NOTAS TÉCNICAS**

### **Patrones Utilizados:**
- **Component Pattern** - Cada funcionalidad en su propio componente
- **Event-Driven Architecture** - Comunicación entre componentes
- **Repository Pattern** - Acceso a datos a través de modelos
- **Validation Pattern** - Reglas centralizadas de validación

### **Optimizaciones:**
- **Eager Loading** - Carga relaciones para evitar N+1
- **Lazy Loading** - Modales solo cuando se necesitan
- **Session Flash** - Mensajes temporales sin persistencia
- **Route Model Binding** - Validación automática de parámetros
