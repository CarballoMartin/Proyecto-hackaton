<?php

namespace App\Services;

use App\Interfaces\SmsServiceInterface;
use Twilio\Rest\Client;

class TwilioSmsService implements SmsServiceInterface
{
    protected $client;
    protected $from;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
        $this->from = config('services.twilio.from');
    }

    public function sendSms(string $to, string $message): bool
    {
        try {
            $this->client->messages->create($to, [
                'from' => $this->from,
                'body' => $message,
            ]);
            return true;
        } catch (\Exception $e) {
            \Log::error("Error enviando SMS: " . $e->getMessage());
            return false;
        }
    }
}
