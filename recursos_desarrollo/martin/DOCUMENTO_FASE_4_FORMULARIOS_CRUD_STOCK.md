# 📋 DOCUMENTO FASE 4: Formularios CRUD de Stock Animal

## 🎯 **OBJETIVO DE LA FASE**
Implementar formularios completos para crear, editar, ver y eliminar registros de stock animal, incluyendo validaciones avanzadas, modales dinámicos y relaciones complejas entre especies, razas y categorías.

---

## 🛠️ **TECNOLOGÍAS UTILIZADAS**

### **Backend:**
- **Laravel 10.x** - Framework PHP principal
- **Livewire 3.x** - Para interfaces dinámicas sin JavaScript
- **Eloquent ORM** - Manejo de base de datos orientado a objetos
- **Laravel Validation** - Validación de formularios avanzada
- **Laravel Events** - Comunicación entre componentes
- **Database Seeders** - Datos de ejemplo para pruebas

### **Frontend:**
- **Tailwind CSS** - Framework CSS utility-first
- **Blade Templates** - Motor de plantillas de Laravel
- **Modales dinámicos** - Para confirmaciones y detalles
- **Formularios reactivos** - Actualización en tiempo real
- **Validación en tiempo real** - Feedback inmediato al usuario

---

## 📁 **ARCHIVOS CREADOS/MODIFICADOS**

### **1. Componente CrearStock**

#### **CrearStock.php**
**Archivo:** `app/Livewire/Productor/Stock/CrearStock.php`

```php
<?php
namespace App\Livewire\Productor\Stock;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Productor;
use App\Models\StockAnimal;
use App\Models\Especie;
use App\Models\Raza;
use App\Models\CategoriaAnimal;
use App\Models\TipoRegistro;
use App\Models\Campo;

#[Layout('layouts.app')] // ← Define el layout a usar
class CrearStock extends Component
{
    // ← Propiedades para el formulario
    public $especie_id, $categoria_id, $raza_id, $tipo_registro_id;
    public $cantidad, $fecha_registro, $observaciones;
    public $campo_id; // ← Relación con campo opcional
    
    // ← Catálogos de datos
    public $especies = [], $razas = [], $categorias = [], $tipos_registro = [];
    public $campos = []; // ← Campos del productor
    
    // ← Propiedades para filtros dinámicos
    public $razas_por_especie = [];
    public $categorias_por_especie = [];
    
    public $productor;

    public function mount()
    {
        // ← Se ejecuta cuando el componente se inicializa
        $user = auth()->user();
        $this->productor = Productor::where('usuario_id', $user->id)->first();
        
        if ($this->productor) {
            // ← Carga catálogos de datos
            $this->especies = Especie::all();
            $this->tipos_registro = TipoRegistro::all();
            $this->campos = Campo::where('productor_id', $this->productor->id)
                                ->where('activo', true)
                                ->get();
            
            // ← Establece fecha por defecto
            $this->fecha_registro = now()->format('Y-m-d');
        }
    }

    public function updatedEspecieId($value)
    {
        // ← Se ejecuta cuando cambia la especie seleccionada
        if ($value) {
            // ← Carga razas de la especie seleccionada
            $this->razas_por_especie = Raza::where('especie_id', $value)->get();
            
            // ← Carga categorías de la especie seleccionada
            $this->categorias_por_especie = CategoriaAnimal::where('especie_id', $value)->get();
            
            // ← Limpia selecciones dependientes
            $this->raza_id = null;
            $this->categoria_id = null;
        } else {
            $this->razas_por_especie = [];
            $this->categorias_por_especie = [];
            $this->raza_id = null;
            $this->categoria_id = null;
        }
    }

    public function rules()
    {
        // ← Define reglas de validación
        return [
            'especie_id' => 'required|exists:especies,id',
            'categoria_id' => 'required|exists:categoria_animals,id',
            'raza_id' => 'required|exists:razas,id',
            'tipo_registro_id' => 'required|exists:tipo_registros,id',
            'cantidad' => 'required|integer|min:1|max:999999',
            'fecha_registro' => 'required|date|before_or_equal:today',
            'observaciones' => 'nullable|string|max:1000',
            'campo_id' => 'nullable|exists:campos,id',
        ];
    }

    public function messages()
    {
        // ← Define mensajes personalizados de validación
        return [
            'especie_id.required' => 'Debe seleccionar una especie.',
            'categoria_id.required' => 'Debe seleccionar una categoría.',
            'raza_id.required' => 'Debe seleccionar una raza.',
            'tipo_registro_id.required' => 'Debe seleccionar un tipo de registro.',
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.integer' => 'La cantidad debe ser un número entero.',
            'cantidad.min' => 'La cantidad debe ser mayor a 0.',
            'cantidad.max' => 'La cantidad no puede exceder 999,999.',
            'fecha_registro.required' => 'La fecha de registro es obligatoria.',
            'fecha_registro.before_or_equal' => 'La fecha no puede ser futura.',
        ];
    }

    public function crearStock()
    {
        // ← Valida los datos del formulario
        $validatedData = $this->validate();
        
        try {
            // ← Crea el registro de stock con los datos validados
            $stock = new StockAnimal($validatedData);
            $stock->productor_id = $this->productor->id;
            $stock->es_actualizacion_periodica = false; // ← Por defecto no es periódico
            $stock->save();
            
            // ← Mensaje de éxito
            session()->flash('message', 'Registro de stock creado exitosamente.');
            
            // ← Redirige a la lista de stock
            return redirect()->route('productor.stock');
            
        } catch (\Exception $e) {
            // ← Mensaje de error si algo falla
            session()->flash('error', 'Error al crear el registro de stock: ' . $e->getMessage());
        }
    }

    public function cancelar()
    {
        // ← Redirige de vuelta a la lista sin guardar
        return redirect()->route('productor.stock');
    }

    public function render()
    {
        return view('livewire.productor.stock.crear-stock');
    }
}
```

**Explicación línea por línea:**
- `public $especie_id, $categoria_id, $raza_id, $tipo_registro_id;` - Propiedades que se sincronizan con inputs del formulario
- `public $campo_id;` - Relación opcional con campo del productor
- `public $razas_por_especie = [];` - Array dinámico para razas según especie seleccionada
- `public function updatedEspecieId($value)` - Método que se ejecuta automáticamente cuando cambia especie_id
- `$this->razas_por_especie = Raza::where('especie_id', $value)->get();` - Carga razas filtradas por especie
- `$this->raza_id = null;` - Limpia selección de raza cuando cambia especie
- `'cantidad' => 'required|integer|min:1|max:999999'` - Validación con límites realistas
- `'fecha_registro' => 'before_or_equal:today'` - Previene fechas futuras
- `$stock->es_actualizacion_periodica = false;` - Por defecto no es actualización periódica

#### **crear-stock.blade.php**
**Archivo:** `resources/views/livewire/productor/stock/crear-stock.blade.php`

```blade
<div>
    {{-- ← Encabezado --}}
    <div class="bg-white shadow-sm border-b mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <h1 class="text-3xl font-bold text-gray-900">Crear Registro de Stock Animal</h1>
                <p class="mt-1 text-sm text-gray-600">Agrega un nuevo registro a tu inventario de animales.</p>
            </div>
        </div>
    </div>

    {{-- ← Formulario --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-xl p-6">
            <form wire:submit="crearStock">
                {{-- ← Información del Animal --}}
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Información del Animal</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {{-- ← Especie --}}
                        <div>
                            <label for="especie_id" class="block text-sm font-medium text-gray-700">Especie *</label>
                            <select wire:model.live="especie_id" id="especie_id" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Seleccione una especie</option>
                                @foreach($especies as $especie)
                                    <option value="{{ $especie->id }}">{{ $especie->nombre }}</option>
                                @endforeach
                            </select>
                            @error('especie_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- ← Categoría --}}
                        <div>
                            <label for="categoria_id" class="block text-sm font-medium text-gray-700">Categoría *</label>
                            <select wire:model="categoria_id" id="categoria_id" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    {{ empty($categorias_por_especie) ? 'disabled' : '' }}>
                                <option value="">Seleccione una categoría</option>
                                @foreach($categorias_por_especie as $categoria)
                                    <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                @endforeach
                            </select>
                            @error('categoria_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- ← Raza --}}
                        <div>
                            <label for="raza_id" class="block text-sm font-medium text-gray-700">Raza *</label>
                            <select wire:model="raza_id" id="raza_id" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    {{ empty($razas_por_especie) ? 'disabled' : '' }}>
                                <option value="">Seleccione una raza</option>
                                @foreach($razas_por_especie as $raza)
                                    <option value="{{ $raza->id }}">{{ $raza->nombre }}</option>
                                @endforeach
                            </select>
                            @error('raza_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- ← Información del Registro --}}
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Información del Registro</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {{-- ← Cantidad --}}
                        <div>
                            <label for="cantidad" class="block text-sm font-medium text-gray-700">Cantidad *</label>
                            <input type="number" wire:model="cantidad" id="cantidad" min="1" max="999999"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Ej: 50">
                            @error('cantidad') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- ← Fecha de Registro --}}
                        <div>
                            <label for="fecha_registro" class="block text-sm font-medium text-gray-700">Fecha de Registro *</label>
                            <input type="date" wire:model="fecha_registro" id="fecha_registro"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error('fecha_registro') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- ← Tipo de Registro --}}
                        <div>
                            <label for="tipo_registro_id" class="block text-sm font-medium text-gray-700">Tipo de Registro *</label>
                            <select wire:model="tipo_registro_id" id="tipo_registro_id" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Seleccione un tipo</option>
                                @foreach($tipos_registro as $tipo)
                                    <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                @endforeach
                            </select>
                            @error('tipo_registro_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- ← Información Adicional --}}
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Información Adicional</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- ← Campo (Opcional) --}}
                        <div>
                            <label for="campo_id" class="block text-sm font-medium text-gray-700">Campo (Opcional)</label>
                            <select wire:model="campo_id" id="campo_id" 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Sin asignar a campo específico</option>
                                @foreach($campos as $campo)
                                    <option value="{{ $campo->id }}">{{ $campo->localidad ?: 'Campo ID: ' . $campo->id }}</option>
                                @endforeach
                            </select>
                            @error('campo_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- ← Observaciones --}}
                        <div>
                            <label for="observaciones" class="block text-sm font-medium text-gray-700">Observaciones</label>
                            <textarea wire:model="observaciones" id="observaciones" rows="3"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                      placeholder="Observaciones adicionales..."></textarea>
                            @error('observaciones') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- ← Información de Ayuda --}}
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong>Consejo:</strong> Primero selecciona la especie para que se carguen automáticamente las razas y categorías disponibles.
                            </p>
                        </div>
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
                        Crear Registro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
```

**Explicación línea por línea:**
- `<form wire:submit="crearStock">` - Envía formulario al método crearStock
- `wire:model.live="especie_id"` - Actualización en tiempo real cuando cambia
- `{{ empty($categorias_por_especie) ? 'disabled' : '' }}` - Deshabilita dropdown si no hay datos
- `@error('especie_id')` - Muestra errores de validación específicos
- `class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3"` - Grid responsive
- `placeholder="Ej: 50"` - Ejemplo para guiar al usuario
- **Información de ayuda** - Consejo visual para mejorar UX

### **2. Componente EditarStock**

#### **EditarStock.php**
**Archivo:** `app/Livewire/Productor/Stock/EditarStock.php`

```php
<?php
namespace App\Livewire\Productor\Stock;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Productor;
use App\Models\StockAnimal;
use App\Models\Especie;
use App\Models\Raza;
use App\Models\CategoriaAnimal;
use App\Models\TipoRegistro;
use App\Models\Campo;

#[Layout('layouts.app')]
class EditarStock extends Component
{
    // ← Propiedades para el formulario
    public $stock_id;
    public $especie_id, $categoria_id, $raza_id, $tipo_registro_id;
    public $cantidad, $fecha_registro, $observaciones;
    public $campo_id;
    
    // ← Catálogos de datos
    public $especies = [], $razas = [], $categorias = [], $tipos_registro = [];
    public $campos = [];
    
    // ← Propiedades para filtros dinámicos
    public $razas_por_especie = [];
    public $categorias_por_especie = [];
    
    public $productor, $stock;

    public function mount($id)
    {
        // ← Se ejecuta cuando el componente se inicializa con el ID
        $user = auth()->user();
        $this->productor = Productor::where('usuario_id', $user->id)->first();
        
        if ($this->productor) {
            // ← Busca el stock y verifica permisos
            $this->stock = StockAnimal::where('id', $id)
                                     ->where('productor_id', $this->productor->id)
                                     ->first();
            
            if (!$this->stock) {
                // ← Si no encuentra el stock, redirige con error
                session()->flash('error', 'Registro de stock no encontrado o no tienes permisos para editarlo.');
                return redirect()->route('productor.stock');
            }
            
            // ← Carga datos del stock en las propiedades
            $this->stock_id = $this->stock->id;
            $this->especie_id = $this->stock->especie_id;
            $this->categoria_id = $this->stock->categoria_id;
            $this->raza_id = $this->stock->raza_id;
            $this->tipo_registro_id = $this->stock->tipo_registro_id;
            $this->cantidad = $this->stock->cantidad;
            $this->fecha_registro = $this->stock->fecha_registro->format('Y-m-d');
            $this->observaciones = $this->stock->observaciones;
            $this->campo_id = $this->stock->campo_id;
            
            // ← Carga catálogos de datos
            $this->especies = Especie::all();
            $this->tipos_registro = TipoRegistro::all();
            $this->campos = Campo::where('productor_id', $this->productor->id)
                                ->where('activo', true)
                                ->get();
            
            // ← Carga filtros dinámicos basados en la especie actual
            $this->cargarFiltrosDinamicos();
        }
    }

    public function updatedEspecieId($value)
    {
        // ← Se ejecuta cuando cambia la especie seleccionada
        if ($value) {
            // ← Carga razas de la especie seleccionada
            $this->razas_por_especie = Raza::where('especie_id', $value)->get();
            
            // ← Carga categorías de la especie seleccionada
            $this->categorias_por_especie = CategoriaAnimal::where('especie_id', $value)->get();
            
            // ← Limpia selecciones dependientes si cambió la especie
            if ($this->especie_id != $this->stock->especie_id) {
                $this->raza_id = null;
                $this->categoria_id = null;
            }
        } else {
            $this->razas_por_especie = [];
            $this->categorias_por_especie = [];
            $this->raza_id = null;
            $this->categoria_id = null;
        }
    }

    private function cargarFiltrosDinamicos()
    {
        // ← Carga filtros dinámicos basados en la especie actual
        if ($this->especie_id) {
            $this->razas_por_especie = Raza::where('especie_id', $this->especie_id)->get();
            $this->categorias_por_especie = CategoriaAnimal::where('especie_id', $this->especie_id)->get();
        }
    }

    public function actualizarStock()
    {
        // ← Valida los datos del formulario
        $validatedData = $this->validate();
        
        try {
            // ← Actualiza el registro de stock con los datos validados
            $this->stock->update($validatedData);
            
            // ← Mensaje de éxito
            session()->flash('message', 'Registro de stock actualizado exitosamente.');
            
            // ← Redirige a la lista de stock
            return redirect()->route('productor.stock');
            
        } catch (\Exception $e) {
            // ← Mensaje de error si algo falla
            session()->flash('error', 'Error al actualizar el registro de stock: ' . $e->getMessage());
        }
    }

    public function cancelar()
    {
        // ← Redirige de vuelta a la lista sin guardar
        return redirect()->route('productor.stock');
    }

    public function render()
    {
        return view('livewire.productor.stock.editar-stock');
    }
}
```

**Explicación línea por línea:**
- `public function mount($id)` - Recibe el ID del stock a editar
- `->where('productor_id', $this->productor->id)` - Verifica que el stock pertenezca al productor
- `$this->fecha_registro = $this->stock->fecha_registro->format('Y-m-d');` - Formatea fecha para input date
- `$this->cargarFiltrosDinamicos();` - Carga filtros basados en datos existentes
- `if ($this->especie_id != $this->stock->especie_id)` - Solo limpia si cambió la especie
- `$this->stock->update($validatedData);` - Actualiza el registro existente

### **3. Componente VerStock (Modal)**

#### **VerStock.php**
**Archivo:** `app/Livewire/Productor/Stock/VerStock.php`

```php
<?php
namespace App\Livewire\Productor\Stock;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Productor;
use App\Models\StockAnimal;

#[On('verStock')] // ← Escucha el evento 'verStock'
class VerStock extends Component
{
    // ← Propiedades para el modal
    public $stock_id;
    public $stock;
    public $productor;
    public $showModal = false;
    
    // ← Propiedades para mostrar datos
    public $especie, $categoria, $raza, $tipo_registro;
    public $cantidad, $fecha_registro, $observaciones;
    public $campo, $es_actualizacion_periodica;
    public $created_at, $updated_at;

    public function mount()
    {
        // ← Obtiene el productor asociado al usuario autenticado
        $user = auth()->user();
        $this->productor = Productor::where('usuario_id', $user->id)->first();
    }

    public function verStock($id)
    {
        // ← Verifica que el stock pertenezca al productor
        $this->stock = StockAnimal::where('id', $id)
                                 ->where('productor_id', $this->productor->id)
                                 ->with(['especie', 'categoria', 'raza', 'tipoRegistro', 'campo'])
                                 ->first();
        
        if ($this->stock) {
            // ← Carga todos los datos del stock
            $this->stock_id = $this->stock->id;
            $this->especie = $this->stock->especie->nombre;
            $this->categoria = $this->stock->categoria->nombre;
            $this->raza = $this->stock->raza->nombre;
            $this->tipo_registro = $this->stock->tipoRegistro->nombre;
            $this->cantidad = $this->stock->cantidad;
            $this->fecha_registro = $this->stock->fecha_registro;
            $this->observaciones = $this->stock->observaciones;
            $this->campo = $this->stock->campo ? $this->stock->campo->localidad : null;
            $this->es_actualizacion_periodica = $this->stock->es_actualizacion_periodica;
            $this->created_at = $this->stock->created_at;
            $this->updated_at = $this->stock->updated_at;
            
            // ← Abre el modal
            $this->showModal = true;
        } else {
            // ← Si no encuentra el stock, muestra error
            session()->flash('error', 'Registro de stock no encontrado o no tienes permisos para verlo.');
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
        return view('livewire.productor.stock.ver-stock');
    }
}
```

**Explicación línea por línea:**
- `#[On('verStock')]` - Escucha el evento 'verStock' de otros componentes
- `->with(['especie', 'categoria', 'raza', 'tipoRegistro', 'campo'])` - Eager loading para evitar N+1
- `$this->campo = $this->stock->campo ? $this->stock->campo->localidad : null;` - Manejo seguro de relación opcional
- `$this->showModal = true;` - Controla la visibilidad del modal
- `$this->resetExcept(['productor']);` - Limpia propiedades manteniendo productor

### **4. Componente EliminarStock (Modal)**

#### **EliminarStock.php**
**Archivo:** `app/Livewire/Productor/Stock/EliminarStock.php`

```php
<?php
namespace App\Livewire\Productor\Stock;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Productor;
use App\Models\StockAnimal;

#[On('eliminarStock')] // ← Escucha el evento 'eliminarStock'
class EliminarStock extends Component
{
    // ← Propiedades para el modal
    public $stock_id;
    public $stock;
    public $productor;
    public $showModal = false;
    public $descripcion_stock; // ← Descripción del stock para mostrar en confirmación

    public function mount()
    {
        // ← Obtiene el productor asociado al usuario autenticado
        $user = auth()->user();
        $this->productor = Productor::where('usuario_id', $user->id)->first();
    }

    public function confirmarEliminacion($id)
    {
        // ← Verifica que el stock pertenezca al productor
        $this->stock = StockAnimal::where('id', $id)
                                 ->where('productor_id', $this->productor->id)
                                 ->with(['especie', 'categoria', 'raza'])
                                 ->first();
        
        if ($this->stock) {
            // ← Carga datos del stock para mostrar en confirmación
            $this->stock_id = $this->stock->id;
            $this->descripcion_stock = sprintf(
                '%s - %s - %s (Cantidad: %s)',
                $this->stock->especie->nombre,
                $this->stock->categoria->nombre,
                $this->stock->raza->nombre,
                number_format($this->stock->cantidad)
            );
            
            // ← Abre el modal de confirmación
            $this->showModal = true;
        } else {
            // ← Si no encuentra el stock, muestra error
            session()->flash('error', 'Registro de stock no encontrado o no tienes permisos para eliminarlo.');
        }
    }

    public function eliminarStock()
    {
        try {
            // ← Verifica nuevamente que el stock pertenezca al productor
            $stock = StockAnimal::where('id', $this->stock_id)
                               ->where('productor_id', $this->productor->id)
                               ->first();
            
            if ($stock) {
                // ← Elimina el stock
                $stock->delete();
                
                // ← Mensaje de éxito
                session()->flash('message', 'Registro de stock eliminado exitosamente.');
                
                // ← Cierra el modal
                $this->cerrarModal();
                
                // ← Emite evento para refrescar la lista
                $this->dispatch('stockEliminado');
                
            } else {
                session()->flash('error', 'Registro de stock no encontrado o no tienes permisos para eliminarlo.');
            }
            
        } catch (\Exception $e) {
            // ← Mensaje de error si algo falla
            session()->flash('error', 'Error al eliminar el registro de stock: ' . $e->getMessage());
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
        return view('livewire.productor.stock.eliminar-stock');
    }
}
```

**Explicación línea por línea:**
- `public function confirmarEliminacion($id)` - Abre modal de confirmación
- `sprintf('%s - %s - %s (Cantidad: %s)', ...)` - Formatea descripción legible
- `number_format($this->stock->cantidad)` - Formatea cantidad con separadores
- `$stock->delete();` - Elimina el registro de la base de datos
- `$this->dispatch('stockEliminado');` - Emite evento para refrescar lista
- **Doble verificación** - Verifica permisos antes y después de eliminar

### **5. Seeder de Datos de Ejemplo**

#### **StockAnimalSeeder.php**
**Archivo:** `database/seeders/StockAnimalSeeder.php`

```php
<?php
namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StockAnimal;
use App\Models\Productor;
use App\Models\Especie;
use App\Models\CategoriaAnimal;
use App\Models\Raza;
use App\Models\TipoRegistro;

class StockAnimalSeeder extends Seeder
{
    public function run(): void
    {
        // ← Obtiene el productor de ejemplo
        $productor = Productor::first();
        
        if (!$productor) {
            return; // ← Si no hay productor, no crea datos
        }
        
        // ← Obtiene catálogos
        $ovino = Especie::where('nombre', 'Ovino')->first();
        $caprino = Especie::where('nombre', 'Caprino')->first();
        $manual = TipoRegistro::where('nombre', 'Manual')->first();
        $inicial = TipoRegistro::where('nombre', 'Inicial')->first();
        
        if ($ovino && $caprino && $manual && $inicial) {
            // ← Obtiene categorías y razas de ovinos
            $cordero = CategoriaAnimal::where('nombre', 'Cordero')->where('especie_id', $ovino->id)->first();
            $oveja = CategoriaAnimal::where('nombre', 'Oveja')->where('especie_id', $ovino->id)->first();
            $merino = Raza::where('nombre', 'Merino')->where('especie_id', $ovino->id)->first();
            $corriedale = Raza::where('nombre', 'Corriedale')->where('especie_id', $ovino->id)->first();
            
            // ← Obtiene categorías y razas de caprinos
            $cabra = CategoriaAnimal::where('nombre', 'Cabra')->where('especie_id', $caprino->id)->first();
            $angora = Raza::where('nombre', 'Angora')->where('especie_id', $caprino->id)->first();
            
            // ← Crea registros de ejemplo
            if ($cordero && $merino) {
                StockAnimal::create([
                    'productor_id' => $productor->id,
                    'especie_id' => $ovino->id,
                    'categoria_id' => $cordero->id,
                    'raza_id' => $merino->id,
                    'cantidad' => 25,
                    'fecha_registro' => now()->subDays(30),
                    'tipo_registro_id' => $inicial->id,
                    'observaciones' => 'Stock inicial de corderos Merino',
                    'es_actualizacion_periodica' => false,
                ]);
            }
            
            if ($oveja && $merino) {
                StockAnimal::create([
                    'productor_id' => $productor->id,
                    'especie_id' => $ovino->id,
                    'categoria_id' => $oveja->id,
                    'raza_id' => $merino->id,
                    'cantidad' => 50,
                    'fecha_registro' => now()->subDays(30),
                    'tipo_registro_id' => $inicial->id,
                    'observaciones' => 'Stock inicial de ovejas Merino',
                    'es_actualizacion_periodica' => false,
                ]);
            }
            
            if ($oveja && $corriedale) {
                StockAnimal::create([
                    'productor_id' => $productor->id,
                    'especie_id' => $ovino->id,
                    'categoria_id' => $oveja->id,
                    'raza_id' => $corriedale->id,
                    'cantidad' => 30,
                    'fecha_registro' => now()->subDays(15),
                    'tipo_registro_id' => $manual->id,
                    'observaciones' => 'Adquisición de ovejas Corriedale',
                    'es_actualizacion_periodica' => false,
                ]);
            }
            
            if ($cabra && $angora) {
                StockAnimal::create([
                    'productor_id' => $productor->id,
                    'especie_id' => $caprino->id,
                    'categoria_id' => $cabra->id,
                    'raza_id' => $angora->id,
                    'cantidad' => 15,
                    'fecha_registro' => now()->subDays(7),
                    'tipo_registro_id' => $manual->id,
                    'observaciones' => 'Nuevas cabras Angora para producción de mohair',
                    'es_actualizacion_periodica' => false,
                ]);
            }
        }
    }
}
```

**Explicación línea por línea:**
- `$productor = Productor::first();` - Obtiene el primer productor disponible
- `if (!$productor) { return; }` - Sale si no hay productor
- `->where('nombre', 'Cordero')->where('especie_id', $ovino->id)` - Filtra por nombre y especie
- `now()->subDays(30)` - Fecha de hace 30 días
- **Datos realistas** - Cantidades y observaciones típicas de ganadería

---

## 🔧 **COMANDOS UTILIZADOS**

```bash
# ← Crear seeder de datos de ejemplo
php artisan make:seeder StockAnimalSeeder

# ← Ejecutar seeder
php artisan db:seed --class=StockAnimalSeeder

# ← Iniciar servidor
php artisan serve
```

---

## 📊 **RESULTADO DE LA FASE 4**

### **✅ Funcionalidades Completadas:**
1. **Formulario de creación** completo con validaciones avanzadas
2. **Formulario de edición** con datos precargados y filtros dinámicos
3. **Modal de detalles** con información completa del stock
4. **Modal de confirmación** para eliminación segura
5. **Validaciones en tiempo real** con mensajes personalizados
6. **Relaciones dinámicas** entre especies, razas y categorías
7. **Datos de ejemplo** para pruebas y demostración
8. **Seguridad completa** con verificación de permisos

### **🎯 Aprendizajes Clave:**
- **Formularios reactivos** - Actualización automática de dropdowns
- **Validación avanzada** - Reglas complejas con mensajes personalizados
- **Modales dinámicos** - Para confirmaciones y detalles
- **Relaciones complejas** - Especie → Raza → Categoría
- **Eager Loading** - Carga eficiente de relaciones
- **Eventos Livewire** - Comunicación entre componentes
- **UX mejorada** - Consejos y ayuda visual

### **🔒 Características de Seguridad:**
- **Verificación de propiedad** - Solo edita/elimina sus registros
- **Validación de entrada** - Previene datos maliciosos
- **Doble verificación** - En eliminación y edición
- **Manejo de errores** - Try-catch en operaciones críticas
- **Sanitización de datos** - Formateo seguro de fechas y números

---

## 🔄 **TRANSICIÓN A FASE 5**

La Fase 4 completó el CRUD completo de stock animal. En Fase 5 implementaremos:
- **Reportes y estadísticas** avanzadas
- **Gráficos y visualizaciones** de datos
- **Exportación de datos** (PDF, Excel)
- **Filtros y búsquedas** avanzadas
- **Dashboard mejorado** con métricas

---

## 📝 **NOTAS TÉCNICAS**

### **Patrones Utilizados:**
- **Component Pattern** - Cada funcionalidad en su propio componente
- **Event-Driven Architecture** - Comunicación entre componentes
- **Repository Pattern** - Acceso a datos a través de modelos
- **Validation Pattern** - Reglas centralizadas de validación
- **Modal Pattern** - Para confirmaciones y detalles

### **Optimizaciones:**
- **Eager Loading** - Carga relaciones para evitar N+1
- **Lazy Loading** - Modales solo cuando se necesitan
- **Session Flash** - Mensajes temporales sin persistencia
- **Route Model Binding** - Validación automática de parámetros
- **Formato de fechas** - Consistente en toda la aplicación

### **Estructura de Datos:**
```
StockAnimal
├── productor_id (FK)
├── especie_id (FK)
├── categoria_id (FK)
├── raza_id (FK)
├── tipo_registro_id (FK)
├── campo_id (FK, nullable)
├── cantidad (integer)
├── fecha_registro (date)
├── observaciones (text, nullable)
├── es_actualizacion_periodica (boolean)
└── timestamps
```

### **Flujo de Datos:**
1. **Usuario selecciona especie** → Se cargan razas y categorías
2. **Usuario completa formulario** → Validación en tiempo real
3. **Usuario envía formulario** → Validación completa y guardado
4. **Éxito** → Mensaje y redirección
5. **Error** → Mensaje de error específico
6. **Eventos** → Refrescan listas automáticamente
