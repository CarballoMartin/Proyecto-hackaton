<?php

namespace Tests\Unit;

use App\Models\Productor;
use App\Models\UnidadProductiva;
use App\Models\Especie;
use App\Models\CategoriaAnimal;
use App\Models\Raza;
use App\Models\TipoRegistro;
use App\Models\DeclaracionStock;
use App\Models\StockAnimal;
use App\Services\StockHistoryService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockHistoryServiceTest extends TestCase
{
    use RefreshDatabase;

    private $productor;
    private $up;
    private $ovino;
    private $caprino;
    private $categoria;
    private $raza;
    private $alta;
    private $baja;
    private $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Configuración inicial de modelos base
        $this->productor = Productor::factory()->create();
        $this->up = UnidadProductiva::factory()->create();
        $this->productor->unidadesProductivas()->attach($this->up->id);

        $this->ovino = Especie::factory()->create(['nombre' => 'Ovino']);
        $this->caprino = Especie::factory()->create(['nombre' => 'Caprino']);
        $this->categoria = CategoriaAnimal::factory()->create();
        $this->raza = Raza::factory()->create();
        $this->alta = TipoRegistro::factory()->create(['nombre' => 'alta']);
        $this->baja = TipoRegistro::factory()->create(['nombre' => 'baja']);

        $this->service = new StockHistoryService();

        // Crear un escenario de historial de stock
        $this->crearEscenarioHistorial();
    }

    private function crearEscenarioHistorial()
    {
        // Período 1: Enero 2024 (Stock final: 90)
        $dec1 = DeclaracionStock::factory()->create([
            'productor_id' => $this->productor->id,
            'unidad_productiva_id' => $this->up->id,
            'fecha_declaracion' => '2024-01-31',
            'estado' => 'completada',
        ]);
        StockAnimal::factory()->create([
            'declaracion_id' => $dec1->id, 'unidad_productiva_id' => $this->up->id, 'especie_id' => $this->ovino->id, 'categoria_id' => $this->categoria->id, 'raza_id' => $this->raza->id, 'tipo_registro_id' => $this->alta->id, 'cantidad' => 100, 'fecha_registro' => '2024-01-15'
        ]);
        StockAnimal::factory()->create([
            'declaracion_id' => $dec1->id, 'unidad_productiva_id' => $this->up->id, 'especie_id' => $this->ovino->id, 'categoria_id' => $this->categoria->id, 'raza_id' => $this->raza->id, 'tipo_registro_id' => $this->baja->id, 'cantidad' => 10, 'fecha_registro' => '2024-01-20'
        ]);

        // Período 2: Febrero 2024 (Stock final: 90 + 15 = 105)
        $dec2 = DeclaracionStock::factory()->create([
            'productor_id' => $this->productor->id,
            'unidad_productiva_id' => $this->up->id,
            'fecha_declaracion' => '2024-02-29',
            'estado' => 'completada',
        ]);
        StockAnimal::factory()->create([
            'declaracion_id' => $dec2->id, 'unidad_productiva_id' => $this->up->id, 'especie_id' => $this->ovino->id, 'categoria_id' => $this->categoria->id, 'raza_id' => $this->raza->id, 'tipo_registro_id' => $this->alta->id, 'cantidad' => 20, 'fecha_registro' => '2024-02-10'
        ]);
        StockAnimal::factory()->create([
            'declaracion_id' => $dec2->id, 'unidad_productiva_id' => $this->up->id, 'especie_id' => $this->ovino->id, 'categoria_id' => $this->categoria->id, 'raza_id' => $this->raza->id, 'tipo_registro_id' => $this->baja->id, 'cantidad' => 5, 'fecha_registro' => '2024-02-15'
        ]);
        // Movimiento de otra especie para probar filtros
        StockAnimal::factory()->create([
            'declaracion_id' => $dec2->id, 'unidad_productiva_id' => $this->up->id, 'especie_id' => $this->caprino->id, 'categoria_id' => $this->categoria->id, 'raza_id' => $this->raza->id, 'tipo_registro_id' => $this->alta->id, 'cantidad' => 50, 'fecha_registro' => '2024-02-20'
        ]);
    }

    public function test_calcula_correctamente_el_stock_en_una_fecha_especifica()
    {
        // Stock a mitad de Enero (solo la primera alta)
        $stock = $this->service->getStockAt($this->productor, Carbon::parse('2024-01-18'));
        $this->assertEquals(100, $stock);

        // Stock al final de Enero (alta - baja)
        $stock = $this->service->getStockAt($this->productor, Carbon::parse('2024-01-31'));
        $this->assertEquals(90, $stock);

        // Stock a mitad de Febrero (Stock Enero + alta de Feb de ovinos)
        $stock = $this->service->getStockAt($this->productor, Carbon::parse('2024-02-12'));
        $this->assertEquals(110, $stock);

        // Stock al final de Febrero (Stock Enero + todos los movimientos de Feb)
        $stock = $this->service->getStockAt($this->productor, Carbon::parse('2024-02-29'));
        $this->assertEquals(155, $stock);
    }

    public function test_calcula_correctamente_la_evolucion_del_stock()
    {
        $evolucion = $this->service->getEvolutionBetween(
            $this->productor, 
            Carbon::parse('2024-01-01'), 
            Carbon::parse('2024-03-31')
        );

        // Comprobar labels (meses)
        $this->assertEquals(['Jan 2024', 'Feb 2024', 'Mar 2024'], $evolucion['labels']);

        // Comprobar data (stock acumulado)
        // Ene: 100 - 10 = 90
        // Feb: 90 + (20 - 5) + 50 = 155
        // Mar: Sin movimientos, se mantiene en 155
        $this->assertEquals([90, 155, 155], $evolucion['datasets'][0]['data']);
    }

    public function test_la_evolucion_del_stock_se_filtra_por_especie()
    {
        $evolucion = $this->service->getEvolutionBetween(
            $this->productor, 
            Carbon::parse('2024-01-01'), 
            Carbon::parse('2024-03-31'),
            ['especie_id' => $this->ovino->id] // Filtrar solo por Ovinos
        );

        // Comprobar data (stock acumulado solo de ovinos)
        // Ene: 100 - 10 = 90
        // Feb: 90 + (20 - 5) = 105
        // Mar: Sin movimientos, se mantiene en 105
        $this->assertEquals([90, 105, 105], $evolucion['datasets'][0]['data']);
    }
}
