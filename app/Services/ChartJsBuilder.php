<?php

namespace App\Services;

use App\Interfaces\ChartBuilderInterface;

class ChartJsBuilder implements ChartBuilderInterface
{
    // Colores base para los gráficos, se pueden extender o personalizar.
    private const COLORS = [
        'rgba(79, 70, 229, 0.8)',  // Indigo
        'rgba(59, 130, 246, 0.8)', // Blue
        'rgba(34, 197, 94, 0.8)',  // Green
        'rgba(245, 158, 11, 0.8)', // Amber
        'rgba(239, 68, 68, 0.8)',   // Red
        'rgba(139, 92, 246, 0.8)',  // Violet
    ];

    public function buildPieChart(string $title, array $labels, array $data): array
    {
        return [
            'type' => 'pie',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => $title,
                    'data' => $data,
                    'backgroundColor' => self::COLORS,
                    'borderColor' => 'white',
                    'borderWidth' => 2,
                ]],
            ],
            'options' => [
                'responsive' => true,
                'plugins' => [
                    'legend' => [
                        'position' => 'top',
                    ],
                ],
            ],
        ];
    }

    public function buildBarChart(string $title, array $labels, array $data): array
    {
        return [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => $title,
                    'data' => $data,
                    'backgroundColor' => self::COLORS[1], // Blue
                    'borderColor' => str_replace('0.8', '1', self::COLORS[1]),
                    'borderWidth' => 1,
                ]],
            ],
            'options' => [
                'responsive' => true,
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                    ],
                ],
                'plugins' => [
                    'legend' => [
                        'display' => false,
                    ],
                ],
            ],
        ];
    }

    public function buildLineChart(array $labels, array $datasets): array
    {
        $formattedDatasets = [];
        foreach ($datasets as $index => $dataset) {
            $color = self::COLORS[$index % count(self::COLORS)];
            $formattedDatasets[] = [
                'label' => $dataset['label'],
                'data' => $dataset['data'],
                'borderColor' => $color,
                'backgroundColor' => str_replace('0.8', '0.1', $color),
                'fill' => true,
                'tension' => 0.4,
            ];
        }

        return [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => $formattedDatasets,
            ],
            'options' => [
                'responsive' => true,
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                    ],
                ],
            ],
        ];
    }
}
