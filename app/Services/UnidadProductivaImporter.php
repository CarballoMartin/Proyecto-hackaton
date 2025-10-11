<?php

namespace App\Services;

use App\Models\Productor;
use App\Models\TipoIdentificador;
use App\Models\UnidadProductiva;
use App\Models\FuenteAgua;
use App\Models\TipoPasto;
use App\Models\TipoSuelo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class UnidadProductivaImporter
{
    public array $errores = [];
    public int $importados = 0;
    public int $asociacionesFallidas = 0;

    // Propiedades para almacenar los catálogos en memoria
    private ?int $rnspaTipoId;
    private array $fuentesAguaMap;
    private array $tiposPastoMap;
    private array $tiposSueloMap;

    public function __construct()
    {
        // Cargamos los catálogos una sola vez para optimizar el proceso.
        // El método pluck('id', 'nombre') crea un mapa [nombre => id].
        $this->rnspaTipoId = TipoIdentificador::where('nombre', 'RNSPA')->value('id');
        $this->fuentesAguaMap = FuenteAgua::pluck('id', 'nombre')->all();
        $this->tiposPastoMap = TipoPasto::pluck('id', 'nombre')->all();
        $this->tiposSueloMap = TipoSuelo::pluck('id', 'nombre')->all();
    }

    public function importarDesdeColeccion(Collection $filas): void
    {
        if (!$this->rnspaTipoId) {
            $this->errores[] = "Error Crítico: El tipo de identificador 'RNSPA' no se encuentra en la base de datos.";
            Log::critical("Importación cancelada: Tipo de identificador 'RNSPA' no encontrado.");
            return;
        }

        foreach ($filas as $index => $fila) {
            $rowNumber = $index + 1;
            try {
                $rnspa = $fila['rnspa'] ?? null;
                if (empty($rnspa)) {
                    // Si no hay RNSPA en la fila, no podemos crear la unidad productiva.
                    throw new \Exception("La columna 'rnspa' es obligatoria y no puede estar vacía.");
                }

                $productorCuil = $fila['cuil'] ?? null;
                $dni = $fila['dni'] ?? null;

                $productor = null;
                if (!empty($productorCuil)) {
                    $productor = Productor::where('cuil', $productorCuil)->first();
                }
                if (!$productor && !empty($dni)) {
                    $productor = Productor::where('dni', $dni)->first();
                }
                if (!$productor) {
                    throw new \Exception("No se encontró un productor con el DNI/CUIL proporcionado para asociar la UP.");
                }

                // Mapeo de catálogos usando los mapas en memoria
                $aguaHumanoFuenteId = $this->mapearCatalogo($fila['agua_humano_fuente'] ?? null, $this->fuentesAguaMap, 'Fuente de Agua Humano');
                $aguaAnimalFuenteId = $this->mapearCatalogo($fila['agua_animal_fuente'] ?? null, $this->fuentesAguaMap, 'Fuente de Agua Animal');
                $tipoPastoId = $this->mapearCatalogo($fila['tipo_pasto_predominante'] ?? null, $this->tiposPastoMap, 'Tipo de Pasto');
                $tipoSueloId = $this->mapearCatalogo($fila['tipo_suelo_predominante'] ?? null, $this->tiposSueloMap, 'Tipo de Suelo');

                $unidadProductivaData = [
                    'superficie' => $fila['superficie'] ?? 0,
                    'habita' => filter_var($fila['habita_en_up'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'latitud' => (!empty($fila['latitud'] ?? null) && (float)($fila['latitud']) != 0) ? (float) $fila['latitud'] : null,
                    'longitud' => (!empty($fila['longitud'] ?? null) && (float)($fila['longitud']) != 0) ? (float) $fila['longitud'] : null,
                    'activo' => true,
                    // ... otros campos ...
                    'agua_humano_en_casa' => filter_var($fila['agua_humano_en_casa'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'forrageras_predominantes' => $fila['forrageras_predominantes'] ?? null,
                    'agua_humano_fuente_id' => $aguaHumanoFuenteId,
                    'agua_animal_fuente_id' => $aguaAnimalFuenteId,
                    'tipo_pasto_predominante_id' => $tipoPastoId,
                    'tipo_suelo_predominante_id' => $tipoSueloId,
                ];

                $unidadProductiva = UnidadProductiva::updateOrCreate(
                    ['identificador_local' => $rnspa, 'tipo_identificador_id' => $this->rnspaTipoId],
                    $unidadProductivaData
                );

                $unidadProductiva->productores()->syncWithoutDetaching([$productor->id]);

            } catch (Throwable $e) {
                $this->asociacionesFallidas++;
                $errorMessage = "Fila {$rowNumber}: " . $e->getMessage();
                Log::error('Error al importar unidad productiva: ' . $errorMessage);
                $this->errores[] = $errorMessage;
                continue;
            }
        }
    }

    /**
     * Busca el ID en un mapa de catálogo y lanza una excepción si no lo encuentra.
     *
     * @param string|null $valorExcel El valor de la celda del Excel.
     * @param array $mapa El mapa de catálogo [nombre => id].
     * @param string $nombreCatalogo El nombre del catálogo para los mensajes de error.
     * @return int|null
     * @throws \Exception
     */
    private function mapearCatalogo(?string $valorExcel, array $mapa, string $nombreCatalogo): ?int
    {
        if (is_null($valorExcel) || $valorExcel === '') {
            return null; // Permite valores vacíos
        }

        if (!isset($mapa[$valorExcel])) {
            // ADVERTENCIA: El valor no se encontró. Se registrará en el log y se continuará con un valor nulo.
            Log::warning("Valor de catálogo no encontrado para '{$nombreCatalogo}': '{$valorExcel}'. Se asignará un valor nulo a
     la fila correspondiente.");
            return null;
        }

        return $mapa[$valorExcel];
    }
}