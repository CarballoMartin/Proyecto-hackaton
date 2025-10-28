<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración de APIs Ambientales
    |--------------------------------------------------------------------------
    |
    | Configuración para las diferentes APIs ambientales utilizadas en el
    | módulo de monitoreo ambiental.
    |
    */

    'apis' => [
        'nasa_power' => [
            'enabled' => env('NASA_POWER_ENABLED', true),
            'base_url' => 'https://power.larc.nasa.gov/api/',
            'cache_ttl' => 86400, // 24 horas
        ],
        'open_meteo' => [
            'enabled' => env('OPEN_METEO_ENABLED', true),
            'base_url' => 'https://api.open-meteo.com/v1/',
            'cache_ttl' => 43200, // 12 horas
        ],
        'copernicus' => [
            'enabled' => env('COPERNICUS_ENABLED', true),
            'base_url' => 'https://services.sentinel-hub.com/api/v1/',
            'client_id' => env('COPERNICUS_CLIENT_ID', ''),
            'client_secret' => env('COPERNICUS_CLIENT_SECRET', ''),
            'cache_ttl' => 604800, // 7 días
        ],
        'soilgrids' => [
            'enabled' => env('SOILGRIDS_ENABLED', true),
            'base_url' => 'https://rest.isric.org/soilgrids/v2.0/',
            'cache_ttl' => 2592000, // 30 días
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Alertas Ambientales
    |--------------------------------------------------------------------------
    |
    | Umbrales para generar alertas ambientales automáticas.
    |
    */

    'alertas' => [
        'sequia_dias_sin_lluvia' => 15,
        'tormenta_mm_24h' => 50,
        'estres_termico_celsius' => 35,
        'helada_celsius' => 0,
        'viento_kmh' => 40,
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de NDVI
    |--------------------------------------------------------------------------
    |
    | Umbrales para clasificar el índice de vegetación normalizado (NDVI).
    |
    */

    'ndvi' => [
        'umbral_bajo' => 0.2,
        'umbral_medio' => 0.4,
        'umbral_alto' => 0.6,
        'actualizacion_dias' => 5, // Ciclo de Sentinel-2
        'max_nubosidad' => 30, // Porcentaje máximo de nubosidad aceptable
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Cache
    |--------------------------------------------------------------------------
    |
    | Configuración de caché para datos ambientales.
    |
    */

    'cache' => [
        'prefix' => 'ambiental',
        'default_ttl' => 3600, // 1 hora
        'ndvi_ttl' => 604800, // 7 días
        'clima_ttl' => 86400, // 24 horas
        'suelo_ttl' => 2592000, // 30 días
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Notificaciones
    |--------------------------------------------------------------------------
    |
    | Configuración para notificaciones ambientales.
    |
    */

    'notificaciones' => [
        'email_enabled' => env('ALERTAS_EMAIL_ENABLED', true),
        'sms_enabled' => env('ALERTAS_SMS_ENABLED', false),
        'dashboard_enabled' => true,
        'frecuencia_maxima' => 24, // Horas entre notificaciones del mismo tipo
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Certificación Ambiental
    |--------------------------------------------------------------------------
    |
    | Configuración para el sistema de certificación ambiental mejorado.
    |
    */

    'certificacion' => [
        'puntos_maximos' => 400, // Aumentado de 300 a 400
        'categorias' => [
            'agua' => 80,
            'biodiversidad' => 70,
            'eficiencia' => 90,
            'sostenibilidad' => 60,
            'ndvi' => 50, // Nueva categoría
            'clima_alertas' => 50, // Nueva categoría
        ],
        'bonus_ndvi' => [
            'alto' => 20, // NDVI >= 0.6
            'medio' => 10, // NDVI >= 0.4
            'bajo' => 0, // NDVI < 0.4
        ],
    ],
];
