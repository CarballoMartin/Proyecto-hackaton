<?php

namespace App\Interfaces;

interface ChartBuilderInterface
{
    /**
     * Construye un gráfico de torta (pie) o dona (doughnut).
     *
     * @param string $title El título del set de datos.
     * @param array $labels Las etiquetas para cada segmento (ej: ['Ovinos', 'Caprinos']).
     * @param array $data Los valores numéricos para cada segmento (ej: [320, 160]).
     * @return array La configuración del gráfico formateada para la librería de frontend.
     */
    public function buildPieChart(string $title, array $labels, array $data): array;

    /**
     * Construye un gráfico de barras.
     *
     * @param string $title El título del set de datos.
     * @param array $labels Las etiquetas para el eje X (ej: ['Ovejas', 'Carneros', ...]).
     * @param array $data Los valores numéricos para cada barra.
     * @return array La configuración del gráfico formateada.
     */
    public function buildBarChart(string $title, array $labels, array $data): array;

    /**
     * Construye un gráfico de líneas.
     *
     * @param array $labels Las etiquetas para el eje X (ej: ['Ene', 'Feb', ...]).
     * @param array $datasets Un array de sets de datos, cada uno con 'label' y 'data'.
     * @return array La configuración del gráfico formateada.
     */
    public function buildLineChart(array $labels, array $datasets): array;
}
