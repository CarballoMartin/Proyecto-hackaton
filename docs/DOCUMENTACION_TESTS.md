# Documentación de Pruebas Unitarias - Historial de Movimientos

Este documento detalla la suite de pruebas unitarias creada para verificar la correcta funcionalidad de la lógica de negocio relacionada con el historial de movimientos de stock.

**Archivo de Prueba:** `tests/Unit/HistorialMovimientosAccionTest.php`

## Propósito

El objetivo de esta suite de pruebas es garantizar la precisión de los cálculos y filtros aplicados en el historial de movimientos. Proporciona una red de seguridad automatizada para detectar regresiones o errores en el futuro si la lógica de negocio subyacente es modificada.

## Configuración de la Prueba (`setUp`)

Antes de la ejecución de cada test, el método `setUp()` se encarga de crear un entorno de datos controlado y predecible. Este entorno consiste en:

- **1 `Productor`** de prueba.
- **2 `UnidadProductiva`** (`UP1` y `UP2`) asociadas al productor.
- **5 `StockAnimal`** (movimientos de stock) distribuidos de la siguiente manera:
    - **UP1 (Enero 2025):**
        - 10 Nacimientos (Alta)
        - 5 Compras (Alta)
        - 2 Muertes (Baja)
    - **UP2 (Febrero 2025):**
        - 20 Nacimientos (Alta)
        - 8 Ventas (Baja)
- Todos los registros de dependencias necesarios (`User`, `MotivoMovimiento`, `DeclaracionStock`, etc.) para satisfacer las reglas de la base de datos.

## Casos de Prueba Implementados

Se han implementado los siguientes casos de prueba para cubrir los distintos escenarios de la funcionalidad:

1.  **`test_calcula_resumen_correctamente_sin_filtros`**
    - **Verifica:** Que los totales generales y el desglose por motivo en el resumen sean correctos cuando no se aplica ningún filtro.

2.  **`test_filtra_correctamente_por_rango_de_fechas`**
    - **Verifica:** Que al aplicar un filtro de fecha, tanto la lista de movimientos como el resumen solo consideren los registros dentro de ese rango.
    - **Estado Actual:** <span style="color:red;">FALLANDO</span>. Este test actualmente falla y requiere más investigación. La lógica parece no estar filtrando correctamente por fecha.

3.  **`test_filtra_correctamente_por_unidad_productiva`**
    - **Verifica:** Que al filtrar por una `UnidadProductiva` específica, el resumen solo muestre los totales para esa unidad.

4.  **`test_filtra_correctamente_por_flujo`**
    - **Verifica:** Que al filtrar por "Bajas", el resumen de "Altas" sea cero y viceversa.

5.  **`test_filtra_correctamente_por_motivo_especifico`**
    - **Verifica:** Que al filtrar por un motivo concreto (ej. "Compra"), el resumen muestre el total específico para ese motivo.

6.  **`test_devuelve_cero_cuando_no_hay_resultados`**
    - **Verifica:** Que si se aplica un filtro que no tiene coincidencias, los totales del resumen sean cero y la lista de movimientos esté vacía.

## Infraestructura de Testing

Para permitir la ejecución de esta suite de pruebas, fue necesario realizar una configuración inicial en el proyecto, que incluyó:

- Añadir el trait `HasFactory` a los modelos: `Productor`, `UnidadProductiva`, `StockAnimal`, `Especie`, `CategoriaAnimal`, `Raza` y `TipoRegistro`.
- Crear las clases Factory correspondientes para cada uno de estos modelos en el directorio `database/factories/`.

---

# Pruebas Unitarias: StockHistoryService

**Archivo de Prueba:** `tests/Unit/StockHistoryServiceTest.php`

## Propósito General

El objetivo de esta suite es garantizar la fiabilidad y precisión del `StockHistoryService`, que actúa como una "máquina del tiempo" para el inventario. Las pruebas aseguran que el servicio puede calcular correctamente tanto el stock total en una fecha específica del pasado como la evolución de dicho stock a lo largo del tiempo.

## Configuración (`setUp` y `crearEscenarioHistorial`)

La prueba construye un escenario de historial de stock predecible y controlado. Para ello, crea:

- Un `Productor` con una `UnidadProductiva`.
- Especies (`Ovino`, `Caprino`), categorías, razas y tipos de registro (`alta`, `baja`).
- **Dos `DeclaracionStock`** que simulan dos períodos de declaración consecutivos (Enero y Febrero de 2024).
- Una serie de **movimientos de `StockAnimal`** asociados a estas declaraciones, con fechas y cantidades específicas para crear una historia conocida. Por ejemplo:
    - **En Enero:** Un alta de 100 ovinos y una baja de 10, resultando en un stock de 90.
    - **En Febrero:** Un alta de 20 ovinos, una baja de 5 ovinos y un alta de 50 caprinos, resultando en un stock total de 155.

## Detalle de las Pruebas Implementadas

1.  **`test_calcula_correctamente_el_stock_en_una_fecha_especifica()`**
    - **Qué hace:** Llama al método `getStockAt()` con fechas específicas dentro del escenario de prueba (ej. a mitad de enero, a final de febrero).
    - **Qué comprueba:** Utiliza `assertEquals` para verificar que el número de animales devuelto por el servicio coincide exactamente con el stock que debería haber en esa fecha, considerando solo los movimientos ocurridos hasta ese momento.

2.  **`test_calcula_correctamente_la_evolucion_del_stock()`**
    - **Qué hace:** Llama al método `getEvolutionBetween()` pidiendo la evolución mensual para el período de prueba.
    - **Qué comprueba:** Realiza dos aserciones clave:
        1.  Que los `labels` del gráfico corresponden a los meses correctos (ej. `['Jan 2024', 'Feb 2024', ...]`)
        2.  Que el array de `data` contiene la secuencia correcta de stock **acumulado** al final de cada mes (ej. `[90, 155, 155]`).

3.  **`test_la_evolucion_del_stock_se_filtra_por_especie()`**
    - **Qué hace:** Llama a `getEvolutionBetween()` de nuevo, pero esta vez le pasa un filtro para que solo considere la especie `Ovino`.
    - **Qué comprueba:** Verifica que el array de `data` resultante muestre la evolución correcta excluyendo todos los movimientos de otras especies (como los caprinos), demostrando que la lógica de filtrado del servicio es funcional.

## Infraestructura de Testing (Adicional)

Para habilitar esta suite, se realizaron las siguientes adiciones al entorno de pruebas:

- Se creó el factory `DeclaracionStockFactory`.
- Se añadió el trait `HasFactory` al modelo `ConfiguracionActualizacion` y se creó su correspondiente factory, `ConfiguracionActualizacionFactory`.
- Se depuraron y corrigieron los factories para cumplir con las restricciones `NOT NULL` de la base de datos y las propiedades `$fillable` de los modelos.