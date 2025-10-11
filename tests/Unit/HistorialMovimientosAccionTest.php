<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Productor;
use App\Models\UnidadProductiva;
use App\Models\MotivoMovimiento;
use App\Models\StockAnimal;
use App\Models\DeclaracionStock;
use App\Models\ConfiguracionActualizacion;
use App\Models\Especie;
use App\Models\CategoriaAnimal;
use App\Models\Raza;
use App\Models\TipoRegistro;
use App\Actions\Cuaderno\FiltrarMovimientosHistorialAction;
use App\Actions\Cuaderno\CalcularResumenHistorialAction;
use Carbon\Carbon;

class HistorialMovimientosAccionTest extends TestCase
{
    use RefreshDatabase;

    private $productor;
    private $up1;
    private $up2;
    private $motivos;

    protected function setUp(): void
    {
        parent::setUp();

        // --- Crear Modelos Base ---
        $user = User::factory()->create();
        $this->productor = Productor::factory()->create(['usuario_id' => $user->id]);
        
        $this->up1 = UnidadProductiva::factory()->create();
        $this->up2 = UnidadProductiva::factory()->create();
        $this->productor->unidadesProductivas()->attach([$this->up1->id, $this->up2->id]);

        $this->motivos = collect([
            'Nacimiento' => MotivoMovimiento::create(['nombre' => 'Nacimiento', 'tipo' => 'alta']),
            'Compra' => MotivoMovimiento::create(['nombre' => 'Compra', 'tipo' => 'alta']),
            'Muerte' => MotivoMovimiento::create(['nombre' => 'Muerte', 'tipo' => 'baja']),
            'Venta' => MotivoMovimiento::create(['nombre' => 'Venta', 'tipo' => 'baja']),
        ]);

        $periodo = ConfiguracionActualizacion::create([
            'ultima_actualizacion' => '2025-01-01',
            'proxima_actualizacion' => '2025-03-31',
            'activo' => true,
        ]);

        $declaracion1 = DeclaracionStock::create([
            'productor_id' => $this->productor->id,
            'periodo_id' => $periodo->id,
            'unidad_productiva_id' => $this->up1->id,
            'fecha_declaracion' => '2025-01-10',
            'estado' => 'aprobada',
        ]);

        $declaracion2 = DeclaracionStock::create([
            'productor_id' => $this->productor->id,
            'periodo_id' => $periodo->id,
            'unidad_productiva_id' => $this->up2->id,
            'fecha_declaracion' => '2025-02-05',
            'estado' => 'aprobada',
        ]);

        $especie = Especie::create(['nombre' => 'Ovino']);
        $categoria = CategoriaAnimal::create(['nombre' => 'Cordero', 'especie_id' => $especie->id]);
        $raza = Raza::create(['nombre' => 'Merino', 'especie_id' => $especie->id]);
        $tipoRegistro = TipoRegistro::create(['nombre' => 'Manual']);

        // --- Crear Datos de Movimientos ---
        $baseData = [
            'especie_id' => $especie->id,
            'categoria_id' => $categoria->id,
            'raza_id' => $raza->id,
            'tipo_registro_id' => $tipoRegistro->id,
        ];

        // UP1 - Mes 1 (Enero 2025)
        StockAnimal::create(array_merge($baseData, [
            'declaracion_id' => $declaracion1->id,
            'unidad_productiva_id' => $this->up1->id,
            'motivo_movimiento_id' => $this->motivos['Nacimiento']->id,
            'cantidad' => 10,
            'fecha_registro' => Carbon::create(2025, 1, 15)
        ]));
        StockAnimal::create(array_merge($baseData, [
            'declaracion_id' => $declaracion1->id,
            'unidad_productiva_id' => $this->up1->id,
            'motivo_movimiento_id' => $this->motivos['Compra']->id,
            'cantidad' => 5,
            'fecha_registro' => Carbon::create(2025, 1, 20)
        ]));
        StockAnimal::create(array_merge($baseData, [
            'declaracion_id' => $declaracion1->id,
            'unidad_productiva_id' => $this->up1->id,
            'motivo_movimiento_id' => $this->motivos['Muerte']->id,
            'cantidad' => 2,
            'fecha_registro' => Carbon::create(2025, 1, 25)
        ]));

        // UP2 - Mes 2 (Febrero 2025)
        StockAnimal::create(array_merge($baseData, [
            'declaracion_id' => $declaracion2->id,
            'unidad_productiva_id' => $this->up2->id,
            'motivo_movimiento_id' => $this->motivos['Nacimiento']->id,
            'cantidad' => 20,
            'fecha_registro' => Carbon::create(2025, 2, 10)
        ]));
        StockAnimal::create(array_merge($baseData, [
            'declaracion_id' => $declaracion2->id,
            'unidad_productiva_id' => $this->up2->id,
            'motivo_movimiento_id' => $this->motivos['Venta']->id,
            'cantidad' => 8,
            'fecha_registro' => Carbon::create(2025, 2, 15)
        ]));
    }

    public function test_calcula_resumen_correctamente_sin_filtros()
    {
        $calcularResumen = new CalcularResumenHistorialAction();
        $resumen = $calcularResumen($this->productor, []);

        $this->assertEquals(35, $resumen['altas']['total']);
        $this->assertEquals(10, $resumen['bajas']['total']);
        $this->assertEquals(30, $resumen['altas']['Nacimiento']);
        $this->assertEquals(5, $resumen['altas']['Compra']);
        $this->assertEquals(2, $resumen['bajas']['Muerte']);
        $this->assertEquals(8, $resumen['bajas']['Venta']);
    }

    public function test_filtra_correctamente_por_rango_de_fechas()
    {
        $filtrarMovimientos = new FiltrarMovimientosHistorialAction();
        $calcularResumen = new CalcularResumenHistorialAction();
        $filters = ['fecha_desde' => '2025-02-01', 'fecha_hasta' => '2025-02-28'];

        $movimientos = $filtrarMovimientos($this->productor, $filters, false);
        $resumen = $calcularResumen($this->productor, $filters);

        $this->assertCount(2, $movimientos);
        $this->assertEquals(20, $resumen['altas']['total']);
        $this->assertEquals(8, $resumen['bajas']['total']);
    }

    public function test_filtra_correctamente_por_unidad_productiva()
    {
        $calcularResumen = new CalcularResumenHistorialAction();
        $filters = ['up_id' => $this->up1->id];

        $resumen = $calcularResumen($this->productor, $filters);

        $this->assertEquals(15, $resumen['altas']['total']);
        $this->assertEquals(2, $resumen['bajas']['total']);
    }

    public function test_filtra_correctamente_por_flujo()
    {
        $calcularResumen = new CalcularResumenHistorialAction();
        $filters = ['flujo' => 'baja'];

        $resumen = $calcularResumen($this->productor, $filters);

        $this->assertEquals(0, $resumen['altas']['total']);
        $this->assertEquals(10, $resumen['bajas']['total']);
    }

    public function test_filtra_correctamente_por_motivo_especifico()
    {
        $calcularResumen = new CalcularResumenHistorialAction();
        $filters = ['motivo_id' => $this->motivos['Compra']->id];

        $resumen = $calcularResumen($this->productor, $filters);

        $this->assertEquals(5, $resumen['altas']['total']);
        $this->assertEquals(0, $resumen['bajas']['total']);
        $this->assertEquals(5, $resumen['specific_total']);
    }

    public function test_devuelve_cero_cuando_no_hay_resultados()
    {
        $calcularResumen = new CalcularResumenHistorialAction();
        $filters = ['fecha_desde' => '2028-01-01'];

        $resumen = $calcularResumen($this->productor, $filters);

        $this->assertEquals(0, $resumen['altas']['total']);
        $this->assertEquals(0, $resumen['bajas']['total']);
    }
}
