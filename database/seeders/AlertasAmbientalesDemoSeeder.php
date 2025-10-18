<?php

namespace Database\Seeders;

use App\Models\UnidadProductiva;
use App\Models\AlertaAmbiental;
use Illuminate\Database\Seeder;

class AlertasAmbientalesDemoSeeder extends Seeder
{
    /**
     * Crea alertas de demostración para testing y presentación
     */
    public function run(): void
    {
        $this->command->info('🚨 Creando alertas de demostración...');

        $unidades = UnidadProductiva::whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->whereHas('productores') // Solo unidades con productores
            ->limit(5)
            ->get();

        if ($unidades->isEmpty()) {
            $this->command->warn('No hay unidades productivas con coordenadas y productores.');
            return;
        }

        $alertasCreadas = 0;

        // Alerta 1: Sequía (crítica, no leída)
        if ($unidades->count() >= 1) {
            AlertaAmbiental::create([
                'unidad_productiva_id' => $unidades[0]->id,
                'tipo' => 'sequia',
                'nivel' => 'critico',
                'titulo' => 'Sequía Prolongada',
                'mensaje' => 'Tu campo está en riesgo de sequía. Verifica disponibilidad de agua para los animales.',
                'datos_contexto' => [
                    'temperatura_promedio' => 35,
                    'dias_sin_lluvia' => 18,
                ],
                'fecha_inicio' => now()->subDays(3),
                'activa' => true,
                'leida' => false,
            ]);
            $alertasCreadas++;
        }

        // Alerta 2: Tormenta (alta, no leída)
        if ($unidades->count() >= 2) {
            AlertaAmbiental::create([
                'unidad_productiva_id' => $unidades[1]->id,
                'tipo' => 'tormenta',
                'nivel' => 'alto',
                'titulo' => 'Tormenta Intensa',
                'mensaje' => 'Se espera tormenta intensa. Asegura instalaciones y protege animales vulnerables.',
                'datos_contexto' => [
                    'lluvia_esperada' => 65,
                    'viento_esperado' => 70,
                    'fecha_esperada' => now()->addDay()->format('Y-m-d'),
                ],
                'fecha_inicio' => now()->subHours(12),
                'activa' => true,
                'leida' => false,
            ]);
            $alertasCreadas++;
        }

        // Alerta 3: Estrés térmico (media, leída)
        if ($unidades->count() >= 3) {
            AlertaAmbiental::create([
                'unidad_productiva_id' => $unidades[2]->id,
                'tipo' => 'estres_termico',
                'nivel' => 'medio',
                'titulo' => 'Estrés Térmico',
                'mensaje' => 'Temperaturas extremas pueden afectar el bienestar animal. Aumenta disponibilidad de sombra y agua.',
                'datos_contexto' => [
                    'temperatura_maxima' => 38,
                    'dias_consecutivos' => 4,
                ],
                'fecha_inicio' => now()->subDays(4),
                'activa' => true,
                'leida' => true, // Esta ya fue leída
            ]);
            $alertasCreadas++;
        }

        // Alerta 4: Helada (baja, no leída)
        if ($unidades->count() >= 4) {
            AlertaAmbiental::create([
                'unidad_productiva_id' => $unidades[3]->id,
                'tipo' => 'helada',
                'nivel' => 'bajo',
                'titulo' => 'Riesgo de Helada',
                'mensaje' => 'Se esperan temperaturas bajas. Protege crías recién nacidas.',
                'datos_contexto' => [
                    'temperatura_minima' => 3,
                    'fecha_esperada' => now()->addDays(2)->format('Y-m-d'),
                ],
                'fecha_inicio' => now()->subHours(6),
                'activa' => true,
                'leida' => false,
            ]);
            $alertasCreadas++;
        }

        // Alerta 5: Sequía antigua (inactiva)
        if ($unidades->count() >= 5) {
            AlertaAmbiental::create([
                'unidad_productiva_id' => $unidades[4]->id,
                'tipo' => 'sequia',
                'nivel' => 'critico',
                'titulo' => 'Sequía Prolongada',
                'mensaje' => 'Sequía resuelta.',
                'datos_contexto' => [
                    'temperatura_promedio' => 33,
                    'dias_sin_lluvia' => 16,
                ],
                'fecha_inicio' => now()->subDays(10),
                'fecha_fin' => now()->subDays(3),
                'activa' => false,
                'leida' => true,
            ]);
            $alertasCreadas++;
        }

        $this->command->info("✅ {$alertasCreadas} alertas de demostración creadas");
        
        // Mostrar resumen
        $this->command->newLine();
        $this->command->table(
            ['Tipo', 'Nivel', 'Campo', 'Estado'],
            AlertaAmbiental::with('unidadProductiva')
                ->latest()
                ->limit(5)
                ->get()
                ->map(fn($a) => [
                    $a->obtenerEmoji() . ' ' . ucfirst($a->tipo),
                    ucfirst($a->nivel),
                    $a->unidadProductiva->nombre,
                    $a->activa ? ($a->leida ? 'Activa/Leída' : 'Activa/No Leída') : 'Inactiva',
                ])
        );
    }
}

