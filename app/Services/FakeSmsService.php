<?php

namespace App\Services;

use App\Interfaces\SmsServiceInterface;
use Illuminate\Support\Facades\Log;

class FakeSmsService implements SmsServiceInterface
{
    public function sendSms(string $to, string $message): bool
    {
        // En vez de enviar, lo loguea
        Log::info("[FAKE SMS] To: {$to} | Message: {$message}");
        return true;
    }
}
