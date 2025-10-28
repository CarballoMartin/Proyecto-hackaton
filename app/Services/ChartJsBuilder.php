<?php

namespace App\Services;

use App\Interfaces\ChartBuilderInterface;

class ChartJsBuilder implements ChartBuilderInterface
{
    // Paleta de colores Campo Verde
    private const COLORS = [
        'rgba(34, 197, 94, 0.8)',   // Verde principal
        'rgba(22, 163, 74, 0.8)',   // Verde oscuro
        'rgba(16, 185, 129, 0.8)',  // Verde esmeralda
        'rgba(59, 130, 246, 0.8)',  // Azul
        'rgba(245, 158, 11, 0.8)',  // Amarillo
        'rgba(239, 68, 68, 0.8)',   // Rojo
        'rgba(139, 92, 246, 0.8)',  // Violeta
        'rgba(236, 72, 153, 0.8)',  // Rosa
        'rgba(14, 165, 233, 0.8)',  // Cian
        'rgba(168, 85, 247, 0.8)',  // Púrpura
    ];

    private const BORDER_COLORS = [
        'rgba(34, 197, 94, 1)',
        'rgba(22, 163, 74, 1)',
        'rgba(16, 185, 129, 1)',
        'rgba(59, 130, 246, 1)',
        'rgba(245, 158, 11, 1)',
        'rgba(239, 68, 68, 1)',
        'rgba(139, 92, 246, 1)',
        'rgba(236, 72, 153, 1)',
        'rgba(14, 165, 233, 1)',
        'rgba(168, 85, 247, 1)',
    ];

    /**
     * Construye un gráfico de torta (pie chart).
     *
     * @param string $title
     * @param array $labels
     * @param array $data
     * @param array $options
     * @return array
     */
    public function buildPieChart(string $title, array $labels, array $data, array $options = []): array
    {
        $defaultOptions = [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 20,
                        'usePointStyle' => true,
                    ],
                ],
                'title' => [
                    'display' => true,
                    'text' => $title,
                    'font' => [
                        'size' => 16,
                        'weight' => 'bold',
                    ],
                ],
            ],
        ];

        return [
            'type' => 'pie',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => $title,
                    'data' => $data,
                    'backgroundColor' => array_slice(self::COLORS, 0, count($data)),
                    'borderColor' => array_slice(self::BORDER_COLORS, 0, count($data)),
                    'borderWidth' => 2,
                    'hoverOffset' => 4,
                ]],
            ],
            'options' => array_merge_recursive($defaultOptions, $options),
        ];
    }

    /**
     * Construye un gráfico de dona (doughnut chart).
     *
     * @param string $title
     * @param array $labels
     * @param array $data
     * @param array $options
     * @return array
     */
    public function buildDoughnutChart(string $title, array $labels, array $data, array $options = []): array
    {
        $defaultOptions = [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'cutout' => '50%',
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 20,
                        'usePointStyle' => true,
                    ],
                ],
                'title' => [
                    'display' => true,
                    'text' => $title,
                    'font' => [
                        'size' => 16,
                        'weight' => 'bold',
                    ],
                ],
            ],
        ];

        return [
            'type' => 'doughnut',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => $title,
                    'data' => $data,
                    'backgroundColor' => array_slice(self::COLORS, 0, count($data)),
                    'borderColor' => array_slice(self::BORDER_COLORS, 0, count($data)),
                    'borderWidth' => 2,
                    'hoverOffset' => 4,
                ]],
            ],
            'options' => array_merge_recursive($defaultOptions, $options),
        ];
    }

    /**
     * Construye un gráfico de barras verticales.
     *
     * @param string $title
     * @param array $labels
     * @param array $data
     * @param array $options
     * @return array
     */
    public function buildBarChart(string $title, array $labels, array $data, array $options = []): array
    {
        $defaultOptions = [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.1)',
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'title' => [
                    'display' => true,
                    'text' => $title,
                    'font' => [
                        'size' => 16,
                        'weight' => 'bold',
                    ],
                ],
            ],
        ];

        return [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => $title,
                    'data' => $data,
                    'backgroundColor' => self::COLORS[0],
                    'borderColor' => self::BORDER_COLORS[0],
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                    'borderSkipped' => false,
                ]],
            ],
            'options' => array_merge_recursive($defaultOptions, $options),
        ];
    }

    /**
     * Construye un gráfico de barras horizontales.
     *
     * @param string $title
     * @param array $labels
     * @param array $data
     * @param array $options
     * @return array
     */
    public function buildHorizontalBarChart(string $title, array $labels, array $data, array $options = []): array
    {
        $defaultOptions = [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'indexAxis' => 'y',
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.1)',
                    ],
                ],
                'y' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'title' => [
                    'display' => true,
                    'text' => $title,
                    'font' => [
                        'size' => 16,
                        'weight' => 'bold',
                    ],
                ],
            ],
        ];

        return [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => $title,
                    'data' => $data,
                    'backgroundColor' => self::COLORS[0],
                    'borderColor' => self::BORDER_COLORS[0],
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                ]],
            ],
            'options' => array_merge_recursive($defaultOptions, $options),
        ];
    }

    /**
     * Construye un gráfico de líneas.
     *
     * @param string $title
     * @param array $labels
     * @param array $datasets
     * @param array $options
     * @return array
     */
    public function buildLineChart(string $title, array $labels, array $datasets, array $options = []): array
    {
        $formattedDatasets = [];
        foreach ($datasets as $index => $dataset) {
            $colorIndex = $index % count(self::COLORS);
            $formattedDatasets[] = [
                'label' => $dataset['label'],
                'data' => $dataset['data'],
                'borderColor' => self::BORDER_COLORS[$colorIndex],
                'backgroundColor' => str_replace('0.8', '0.1', self::COLORS[$colorIndex]),
                'fill' => $dataset['fill'] ?? true,
                'tension' => $dataset['tension'] ?? 0.4,
                'borderWidth' => $dataset['borderWidth'] ?? 2,
                'pointBackgroundColor' => self::BORDER_COLORS[$colorIndex],
                'pointBorderColor' => '#fff',
                'pointBorderWidth' => 2,
                'pointRadius' => 4,
                'pointHoverRadius' => 6,
            ];
        }

        $defaultOptions = [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.1)',
                    ],
                ],
                'x' => [
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.1)',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                    ],
                ],
                'title' => [
                    'display' => true,
                    'text' => $title,
                    'font' => [
                        'size' => 16,
                        'weight' => 'bold',
                    ],
                ],
            ],
        ];

        return [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => $formattedDatasets,
            ],
            'options' => array_merge_recursive($defaultOptions, $options),
        ];
    }

    /**
     * Construye un gráfico de área.
     *
     * @param string $title
     * @param array $labels
     * @param array $datasets
     * @param array $options
     * @return array
     */
    public function buildAreaChart(string $title, array $labels, array $datasets, array $options = []): array
    {
        $formattedDatasets = [];
        foreach ($datasets as $index => $dataset) {
            $colorIndex = $index % count(self::COLORS);
            $formattedDatasets[] = [
                'label' => $dataset['label'],
                'data' => $dataset['data'],
                'borderColor' => self::BORDER_COLORS[$colorIndex],
                'backgroundColor' => self::COLORS[$colorIndex],
                'fill' => true,
                'tension' => $dataset['tension'] ?? 0.4,
                'borderWidth' => $dataset['borderWidth'] ?? 2,
            ];
        }

        $defaultOptions = [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'stacked' => true,
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.1)',
                    ],
                ],
                'x' => [
                    'stacked' => true,
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.1)',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                    ],
                ],
                'title' => [
                    'display' => true,
                    'text' => $title,
                    'font' => [
                        'size' => 16,
                        'weight' => 'bold',
                    ],
                ],
            ],
        ];

        return [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => $formattedDatasets,
            ],
            'options' => array_merge_recursive($defaultOptions, $options),
        ];
    }

    /**
     * Construye un gráfico de radar.
     *
     * @param string $title
     * @param array $labels
     * @param array $datasets
     * @param array $options
     * @return array
     */
    public function buildRadarChart(string $title, array $labels, array $datasets, array $options = []): array
    {
        $formattedDatasets = [];
        foreach ($datasets as $index => $dataset) {
            $colorIndex = $index % count(self::COLORS);
            $formattedDatasets[] = [
                'label' => $dataset['label'],
                'data' => $dataset['data'],
                'borderColor' => self::BORDER_COLORS[$colorIndex],
                'backgroundColor' => str_replace('0.8', '0.2', self::COLORS[$colorIndex]),
                'borderWidth' => 2,
                'pointBackgroundColor' => self::BORDER_COLORS[$colorIndex],
                'pointBorderColor' => '#fff',
                'pointBorderWidth' => 2,
                'pointRadius' => 4,
            ];
        }

        $defaultOptions = [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'r' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.1)',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                    ],
                ],
                'title' => [
                    'display' => true,
                    'text' => $title,
                    'font' => [
                        'size' => 16,
                        'weight' => 'bold',
                    ],
                ],
            ],
        ];

        return [
            'type' => 'radar',
            'data' => [
                'labels' => $labels,
                'datasets' => $formattedDatasets,
            ],
            'options' => array_merge_recursive($defaultOptions, $options),
        ];
    }

    /**
     * Construye un gráfico de dispersión (scatter).
     *
     * @param string $title
     * @param array $datasets
     * @param array $options
     * @return array
     */
    public function buildScatterChart(string $title, array $datasets, array $options = []): array
    {
        $formattedDatasets = [];
        foreach ($datasets as $index => $dataset) {
            $colorIndex = $index % count(self::COLORS);
            $formattedDatasets[] = [
                'label' => $dataset['label'],
                'data' => $dataset['data'],
                'borderColor' => self::BORDER_COLORS[$colorIndex],
                'backgroundColor' => self::COLORS[$colorIndex],
                'pointRadius' => 6,
                'pointHoverRadius' => 8,
            ];
        }

        $defaultOptions = [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'x' => [
                    'type' => 'linear',
                    'position' => 'bottom',
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.1)',
                    ],
                ],
                'y' => [
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.1)',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                    ],
                ],
                'title' => [
                    'display' => true,
                    'text' => $title,
                    'font' => [
                        'size' => 16,
                        'weight' => 'bold',
                    ],
                ],
            ],
        ];

        return [
            'type' => 'scatter',
            'data' => [
                'datasets' => $formattedDatasets,
            ],
            'options' => array_merge_recursive($defaultOptions, $options),
        ];
    }

    /**
     * Construye un gráfico de barras apiladas.
     *
     * @param string $title
     * @param array $labels
     * @param array $datasets
     * @param array $options
     * @return array
     */
    public function buildStackedBarChart(string $title, array $labels, array $datasets, array $options = []): array
    {
        $formattedDatasets = [];
        foreach ($datasets as $index => $dataset) {
            $colorIndex = $index % count(self::COLORS);
            $formattedDatasets[] = [
                'label' => $dataset['label'],
                'data' => $dataset['data'],
                'backgroundColor' => self::COLORS[$colorIndex],
                'borderColor' => self::BORDER_COLORS[$colorIndex],
                'borderWidth' => 1,
                'borderRadius' => 4,
            ];
        }

        $defaultOptions = [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'x' => [
                    'stacked' => true,
                    'grid' => [
                        'display' => false,
                    ],
                ],
                'y' => [
                    'stacked' => true,
                    'beginAtZero' => true,
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.1)',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                    ],
                ],
                'title' => [
                    'display' => true,
                    'text' => $title,
                    'font' => [
                        'size' => 16,
                        'weight' => 'bold',
                    ],
                ],
            ],
        ];

        return [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => $formattedDatasets,
            ],
            'options' => array_merge_recursive($defaultOptions, $options),
        ];
    }

    /**
     * Obtiene colores personalizados para gráficos.
     *
     * @param int $count
     * @return array
     */
    public function getCustomColors(int $count): array
    {
        return array_slice(self::COLORS, 0, $count);
    }

    /**
     * Obtiene colores de borde personalizados.
     *
     * @param int $count
     * @return array
     */
    public function getCustomBorderColors(int $count): array
    {
        return array_slice(self::BORDER_COLORS, 0, $count);
    }
}
