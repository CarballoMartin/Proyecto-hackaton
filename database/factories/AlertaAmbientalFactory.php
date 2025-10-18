<?php

namespace Database\Factories;

use App\Models\AlertaAmbiental;
use App\Models\UnidadProductiva;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlertaAmbientalFactory extends Factory
{
    protected $model = AlertaAmbiental::class;

    public function definition(): array
    {
        $tipo = $this->faker->randomElement(['sequia', 'tormenta', 'estres_termico', 'helada']);
        
        return [
            'unidad_productiva_id' => UnidadProductiva::factory(),
            'tipo' => $tipo,
            'nivel' => $this->obtenerNivel($tipo),
            'titulo' => $this->obtenerTitulo($tipo),
            'mensaje' => $this->obtenerMensaje($tipo),
            'datos_contexto' => $this->obtenerContexto($tipo),
            'activa' => true,
            'leida' => false,
            'fecha_inicio' => now(),
            'fecha_fin' => null,
            'notificado_email' => false,
            'notificado_sms' => false,
            'fecha_notificacion' => null,
        ];
    }

    private function obtenerNivel($tipo): string
    {
        return match($tipo) {
            'sequia' => 'critico',
            'tormenta' => 'alto',
            'estres_termico' => 'medio',
            'helada' => 'bajo',
            default => 'bajo',
        };
    }

    private function obtenerTitulo($tipo): string
    {
        return match($tipo) {
            'sequia' => 'Sequía Prolongada',
            'tormenta' => 'Tormenta Intensa',
            'estres_termico' => 'Estrés Térmico',
            'helada' => 'Riesgo de Helada',
            default => 'Alerta Ambiental',
        };
    }

    private function obtenerMensaje($tipo): string
    {
        return match($tipo) {
            'sequia' => 'Tu campo está en riesgo de sequía. Verifica disponibilidad de agua.',
            'tormenta' => 'Se espera tormenta intensa. Asegura instalaciones.',
            'estres_termico' => 'Temperaturas extremas pueden afectar el bienestar animal.',
            'helada' => 'Se esperan temperaturas bajas. Protege crías.',
            default => 'Condición detectada que requiere atención.',
        };
    }

    private function obtenerContexto($tipo): array
    {
        return match($tipo) {
            'sequia' => [
                'temperatura_promedio' => 35,
                'dias_sin_lluvia' => 17,
            ],
            'tormenta' => [
                'lluvia_esperada' => 65,
                'viento_esperado' => 70,
                'fecha_esperada' => now()->addDay()->format('Y-m-d'),
            ],
            'estres_termico' => [
                'temperatura_maxima' => 38,
                'dias_consecutivos' => 4,
            ],
            'helada' => [
                'temperatura_minima' => 3,
                'fecha_esperada' => now()->addDay()->format('Y-m-d'),
            ],
            default => [],
        };
    }

    // States útiles

    public function activa(): static
    {
        return $this->state(['activa' => true, 'fecha_fin' => null]);
    }

    public function inactiva(): static
    {
        return $this->state([
            'activa' => false,
            'fecha_fin' => now()->subHours($this->faker->numberBetween(1, 48)),
        ]);
    }

    public function leida(): static
    {
        return $this->state(['leida' => true]);
    }

    public function noLeida(): static
    {
        return $this->state(['leida' => false]);
    }

    public function notificada(): static
    {
        return $this->state([
            'notificado_email' => true,
            'fecha_notificacion' => now()->subHours($this->faker->numberBetween(1, 12)),
        ]);
    }

    // States por tipo

    public function sequia(): static
    {
        return $this->state([
            'tipo' => 'sequia',
            'nivel' => 'critico',
            'titulo' => 'Sequía Prolongada',
            'mensaje' => 'Tu campo está en riesgo de sequía.',
            'datos_contexto' => [
                'temperatura_promedio' => 35,
                'dias_sin_lluvia' => 17,
            ],
        ]);
    }

    public function tormenta(): static
    {
        return $this->state([
            'tipo' => 'tormenta',
            'nivel' => 'alto',
            'titulo' => 'Tormenta Intensa',
            'mensaje' => 'Se espera tormenta intensa.',
            'datos_contexto' => [
                'lluvia_esperada' => 65,
                'viento_esperado' => 70,
            ],
        ]);
    }

    public function estreTermico(): static
    {
        return $this->state([
            'tipo' => 'estres_termico',
            'nivel' => 'medio',
            'titulo' => 'Estrés Térmico',
            'mensaje' => 'Temperaturas extremas detectadas.',
            'datos_contexto' => [
                'temperatura_maxima' => 38,
                'dias_consecutivos' => 4,
            ],
        ]);
    }

    public function helada(): static
    {
        return $this->state([
            'tipo' => 'helada',
            'nivel' => 'bajo',
            'titulo' => 'Riesgo de Helada',
            'mensaje' => 'Se esperan temperaturas bajas.',
            'datos_contexto' => [
                'temperatura_minima' => 3,
            ],
        ]);
    }
}

