# 📋 DOCUMENTO FASE 1: Dashboard, Perfil y Listado Básico

## 🎯 **OBJETIVO DE LA FASE**
Crear la estructura básica del panel del productor con:
- Dashboard principal con estadísticas
- Gestión de perfil personal
- Listado básico de campos

---

## 🛠️ **TECNOLOGÍAS UTILIZADAS**

### **Backend:**
- **Laravel 10.x** - Framework PHP principal
- **Livewire 3.x** - Para interfaces dinámicas sin JavaScript
- **Eloquent ORM** - Manejo de base de datos orientado a objetos
- **Laravel Validation** - Validación de formularios
- **Laravel Routes** - Sistema de rutas web

### **Frontend:**
- **Tailwind CSS** - Framework CSS utility-first
- **Blade Templates** - Motor de plantillas de Laravel
- **Alpine.js** - (Incluido con Livewire) Para interactividad

---

## 📁 **ARCHIVOS CREADOS/MODIFICADOS**

### **1. Componente Dashboard**
**Archivo:** `app/Livewire/Productor/Dashboard.php`

```php
<?php
namespace App\Livewire\Productor;

use Livewire\Component;
use Livewire\Attributes\Layout; // ← Atributo para definir el layout
use App\Models\Productor;
use App\Models\Campo;
use App\Models\StockAnimal;

#[Layout('layouts.app')] // ← Define que use el layout 'app'
class Dashboard extends Component
{
    public function render()
    {
        // ← Obtiene el usuario autenticado actual
        $user = auth()->user();
        
        // ← Busca el productor asociado al usuario
        $productor = Productor::where('usuario_id', $user->id)->first();
        
        // ← Inicializa variables con valores por defecto
        $totalCampos = 0;
        $totalOvinos = 0;
        $totalCaprinos = 0;
        $ultimaActualizacion = null;
        
        if ($productor) {
            // ← Cuenta campos del productor
            $totalCampos = Campo::where('productor_id', $productor->id)->count();
            
            // ← Obtiene stock animal del productor
            $stockAnimal = StockAnimal::where('productor_id', $productor->id)->get();
            
            // ← Suma ovinos (especie_id = 1)
            $totalOvinos = $stockAnimal->where('especie_id', 1)->sum('cantidad');
            
            // ← Suma caprinos (especie_id = 2)
            $totalCaprinos = $stockAnimal->where('especie_id', 2)->sum('cantidad');
            
            // ← Obtiene fecha de última actualización
            $ultimaActualizacion = $productor->updated_at;
        }
        
        // ← Retorna la vista con datos
        return view('livewire.productor.dashboard', [
            'productor' => $productor,
            'totalCampos' => $totalCampos,
            'totalOvinos' => $totalOvinos,
            'totalCaprinos' => $totalCaprinos,
            'ultimaActualizacion' => $ultimaActualizacion,
        ]);
    }
}
```

**Explicación línea por línea:**
- `namespace App\Livewire\Productor;` - Define el espacio de nombres
- `use Livewire\Component;` - Importa la clase base de Livewire
- `use Livewire\Attributes\Layout;` - Importa el atributo para layouts
- `#[Layout('layouts.app')]` - Define que use el layout 'app.blade.php'
- `$user = auth()->user();` - Obtiene el usuario logueado
- `Productor::where('usuario_id', $user->id)->first();` - Busca productor por ID de usuario
- `Campo::where('productor_id', $productor->id)->count();` - Cuenta campos del productor
- `$stockAnimal->where('especie_id', 1)->sum('cantidad');` - Suma cantidad de ovinos

### **2. Vista Dashboard**
**Archivo:** `resources/views/livewire/productor/dashboard.blade.php`

```blade
<div>
    {{-- ← Comentario Blade que no aparece en HTML --}}
    <div class="bg-white shadow-sm border-b mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                <p class="mt-1 text-sm text-gray-600">Bienvenido a tu panel de productor.</p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($productor)
            {{-- ← Cards de estadísticas --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total de Campos</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $totalCampos }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- ← Más cards similares... --}}
            </div>
        @else
            {{-- ← Mensaje si no hay productor --}}
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <p class="text-sm text-yellow-700">
                    No se encontró información de productor asociada a tu cuenta.
                </p>
            </div>
        @endif
    </div>
</div>
```

**Explicación línea por línea:**
- `<div>` - Contenedor principal del componente Livewire
- `{{-- --}}` - Comentarios Blade que no aparecen en HTML
- `@if($productor)` - Directiva Blade para condicional
- `{{ $totalCampos }}` - Echo de variable PHP en Blade
- `class="grid grid-cols-1 md:grid-cols-3"` - Clases Tailwind para grid responsive
- `@else` - Directiva Blade para caso contrario
- `@endif` - Cierre de directiva condicional

### **3. Componente Perfil**
**Archivo:** `app/Livewire/Productor/Perfil.php`

```php
<?php
namespace App\Livewire\Productor;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Productor;
use App\Models\CondicionTenencia;

#[Layout('layouts.app')]
class Perfil extends Component
{
    // ← Propiedades públicas que se sincronizan con el formulario
    public $productor;
    public $nombre, $dni, $fecha_nacimiento, $municipio, $paraje, $direccion, $celular, $email, $latitud, $longitud, $rnspa, $condicion_tenencia_id;
    public $condiciones_tenencia;

    public function mount()
    {
        // ← Se ejecuta cuando el componente se inicializa
        $user = auth()->user();
        $this->productor = Productor::where('usuario_id', $user->id)->first();
        
        if ($this->productor) {
            // ← Carga datos del productor en las propiedades
            $this->nombre = $this->productor->nombre;
            $this->dni = $this->productor->dni;
            $this->fecha_nacimiento = $this->productor->fecha_nacimiento;
            $this->municipio = $this->productor->municipio;
            $this->paraje = $this->productor->paraje;
            $this->direccion = $this->productor->direccion;
            $this->celular = $this->productor->celular;
            $this->email = $this->productor->email;
            $this->latitud = $this->productor->latitud;
            $this->longitud = $this->productor->longitud;
            $this->rnspa = $this->productor->rnspa;
            $this->condicion_tenencia_id = $this->productor->condicion_tenencia_id;
        }
        
        // ← Carga opciones para el selector
        $this->condiciones_tenencia = CondicionTenencia::all();
    }

    public function rules()
    {
        // ← Define reglas de validación
        return [
            'nombre' => 'required|string|max:255',
            'dni' => 'required|string|max:20',
            'fecha_nacimiento' => 'nullable|date',
            'municipio' => 'required|string|max:255',
            'paraje' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'celular' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'rnspa' => 'nullable|string|max:255',
            'condicion_tenencia_id' => 'required|exists:condicion_tenencias,id',
        ];
    }

    public function actualizarPerfil()
    {
        // ← Valida los datos del formulario
        $validatedData = $this->validate();
        
        try {
            // ← Actualiza el productor con los datos validados
            $this->productor->update($validatedData);
            
            // ← Mensaje de éxito
            session()->flash('message', 'Perfil actualizado exitosamente.');
            
        } catch (\Exception $e) {
            // ← Mensaje de error si algo falla
            session()->flash('error', 'Error al actualizar el perfil: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.productor.perfil');
    }
}
```

**Explicación línea por línea:**
- `public $nombre, $dni, ...` - Propiedades públicas que se sincronizan con inputs
- `public function mount()` - Método que se ejecuta al inicializar el componente
- `$this->productor->update($validatedData)` - Actualiza el registro en la BD
- `session()->flash('message', '...')` - Guarda mensaje en la sesión
- `$this->validate()` - Ejecuta las reglas de validación definidas

### **4. Rutas Actualizadas**
**Archivo:** `routes/web.php`

```php
// ← Rutas para Productor
Route::middleware(['role:productor'])->group(function () {
    // ← Ruta del dashboard
    Route::get('/productor/dashboard', \App\Livewire\Productor\Dashboard::class)->name('productor.dashboard');
    
    // ← Ruta del perfil
    Route::get('/productor/perfil', \App\Livewire\Productor\Perfil::class)->name('productor.perfil');
    
    // ← Ruta de campos
    Route::get('/productor/campos', \App\Livewire\Productor\Campos\ListarCampos::class)->name('productor.campos');
});
```

**Explicación línea por línea:**
- `Route::middleware(['role:productor'])` - Aplica middleware de rol
- `->group(function () {` - Agrupa rutas con middleware común
- `Route::get('/productor/dashboard', ...)` - Define ruta GET
- `\App\Livewire\Productor\Dashboard::class` - Clase Livewire que maneja la ruta
- `->name('productor.dashboard')` - Nombre de la ruta para usar en `route()`

---

## 🔧 **COMANDOS UTILIZADOS**

```bash
# ← Crear componente Livewire
php artisan make:livewire Productor/Dashboard

# ← Crear seeder
php artisan make:seeder ProductorSeeder

# ← Ejecutar seeder específico
php artisan db:seed --class=ProductorSeeder

# ← Iniciar servidor
php artisan serve
```

---

## 📊 **RESULTADO DE LA FASE 1**

### **✅ Funcionalidades Completadas:**
1. **Dashboard funcional** con estadísticas reales
2. **Formulario de perfil** con validación
3. **Listado básico de campos** con datos
4. **Navegación entre secciones** funcionando
5. **Redirección post-login** por roles

### **🎯 Aprendizajes Clave:**
- **Livewire** permite crear interfaces dinámicas sin JavaScript
- **Eloquent ORM** facilita las consultas a la base de datos
- **Blade** es el motor de plantillas de Laravel
- **Tailwind CSS** proporciona clases utilitarias para estilos
- **Middleware** controla el acceso a rutas por roles

---

## 🔄 **TRANSICIÓN A FASE 2**

La Fase 1 estableció la base. Ahora en Fase 2 agregaremos:
- Formularios completos para crear/editar campos
- Validación avanzada
- Catálogos de datos
- Funcionalidades CRUD completas
