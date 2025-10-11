<?php

namespace App\Interfaces;

interface SmsServiceInterface
{
    /**
     * Envía un SMS al número indicado.
     *
     * @param string $to Número de teléfono destino (formato E.164 recomendado: +54911...)
     * @param string $message Contenido del SMS
     * @return bool true si se "envió", false en caso de error
     */
    public function sendSms(string $to, string $message): bool;
}
