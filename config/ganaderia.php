<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración General de Ganadería
    |--------------------------------------------------------------------------
    |
    | Esta configuración define los parámetros generales del sistema de
    | gestión ganadera, independientemente de las especies específicas.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Código de Identificador por Defecto
    |--------------------------------------------------------------------------
    |
    | Define el código del tipo de identificador que se utilizará por defecto
    | en la aplicación, por ejemplo, al registrar nuevos productores.
    | Esto permite centralizar la configuración y facilita la adaptación
    | del sistema a diferentes normativas regionales.
    |
    | Ej: 'RNSPA', 'CUIG', etc.
    |
    */
    'default_identifier_code' => 'RNSPA',

    /*
    |--------------------------------------------------------------------------
    | Configuración de Especies
    |--------------------------------------------------------------------------
    |
    | Configuraciones específicas para cada especie de ganado.
    | Los factores de emisión y otros parámetros se definen aquí.
    |
    */
    'factores_emision' => [
        'Ovino' => [
            'ch4_animal_anio' => 8.0,
            'co2_equivalente' => 28,
            'descripcion' => 'Ovino adulto promedio'
        ],
        'Caprino' => [
            'ch4_animal_anio' => 5.0,
            'co2_equivalente' => 28,
            'descripcion' => 'Caprino adulto promedio'
        ],
        'Bovino' => [
            'ch4_animal_anio' => 57.0,
            'co2_equivalente' => 28,
            'descripcion' => 'Bovino adulto promedio'
        ],
        'Equino' => [
            'ch4_animal_anio' => 18.0,
            'co2_equivalente' => 28,
            'descripcion' => 'Equino adulto promedio'
        ],
        'Porcino' => [
            'ch4_animal_anio' => 1.5,
            'co2_equivalente' => 28,
            'descripcion' => 'Porcino adulto promedio'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Benchmarks Ambientales
    |--------------------------------------------------------------------------
    |
    | Valores de referencia para evaluar el desempeño ambiental
    | (kg CO2eq/animal/año)
    |
    */
    'benchmarks_ambientales' => [
        'excelente' => 150,
        'bueno' => 250,
        'promedio' => 350,
        'mejorable' => 500,
    ],
];


