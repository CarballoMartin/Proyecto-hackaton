<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datos_satelitales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidad_productiva_id')->constrained('unidades_productivas')->cascadeOnDelete();
            
            // Datos del satélite
            $table->string('satelite')->default('Sentinel-2'); // Sentinel-2, Landsat-8, etc.
            $table->date('fecha_imagen'); // Fecha de la imagen satelital
            $table->string('producto_id')->nullable(); // ID del producto en Sentinel Hub
            
            // Índices de vegetación
            $table->decimal('ndvi_promedio', 4, 3)->nullable(); // NDVI promedio del área (0.0-1.0)
            $table->decimal('ndvi_maximo', 4, 3)->nullable(); // NDVI máximo
            $table->decimal('ndvi_minimo', 4, 3)->nullable(); // NDVI mínimo
            $table->decimal('ndvi_desviacion', 4, 3)->nullable(); // Desviación estándar
            
            // Otros índices útiles
            $table->decimal('ndwi', 4, 3)->nullable(); // Índice de agua (Water Index)
            $table->decimal('gci', 4, 3)->nullable(); // Green Chlorophyll Index
            $table->decimal('lai', 4, 2)->nullable(); // Leaf Area Index
            
            // Metadatos de la imagen
            $table->integer('resolucion_metros')->default(10); // Resolución en metros
            $table->decimal('cobertura_nubes', 4, 2)->nullable(); // % de cobertura de nubes
            $table->string('calidad_imagen')->default('good'); // good, medium, poor
            
            // Coordenadas del área analizada
            $table->decimal('latitud_centro', 10, 8);
            $table->decimal('longitud_centro', 11, 8);
            $table->decimal('area_hectareas', 8, 2)->nullable(); // Área analizada en hectáreas
            
            // Análisis y clasificación
            $table->string('estado_vegetacion')->nullable(); // excellent, good, fair, poor
            $table->json('distribucion_ndvi')->nullable(); // Distribución de valores NDVI
            $table->json('alertas_vegetacion')->nullable(); // Alertas detectadas
            
            // URLs de recursos
            $table->string('url_imagen_rgb')->nullable(); // URL de imagen RGB
            $table->string('url_imagen_ndvi')->nullable(); // URL de imagen NDVI
            $table->string('url_metadata')->nullable(); // URL de metadatos
            
            // Control de calidad
            $table->boolean('datos_validos')->default(true);
            $table->text('notas')->nullable(); // Notas sobre la calidad de datos
            
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['unidad_productiva_id', 'fecha_imagen']);
            $table->index(['fecha_imagen', 'satelite']);
            $table->index('ndvi_promedio');
            $table->index('estado_vegetacion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datos_satelitales');
    }
};