<?php

namespace App\Livewire\Auth;

use App\Interfaces\SmsServiceInterface;
use App\Mail\CodigoAccesoMail;
use App\Models\Productor;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class LoginOtp extends Component
{
    public $identificador = '';
    public $codigo = '';
    public $step = 1;
    public $feedbackMessage = '';

    public function solicitarCodigo(SmsServiceInterface $smsService)
    {
        $this->dispatchBrowserEvent('loading-started');

        $this->validate([
            'identificador' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (str_contains($value, '@')) {
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $fail('El formato del email no es válido.');
                        }
                    } else {
                        $digits = preg_replace('/[^0-9]/', '', $value);
                        if (strlen($digits) < 6) {
                            $fail('El identificador no parece un email o teléfono válido.');
                        }
                    }
                },
            ],
        ]);
        $this->reset('feedbackMessage');

        $user = $this->findUserByIdentificador($this->identificador);

        if ($user && $user->hasRole('productor')) { // Asegurarse que es un productor
            $otp = random_int(100000, 999999);
            Cache::put('codigo_acceso_' . $user->id, $otp, now()->addMinutes(10));

            if (filter_var($this->identificador, FILTER_VALIDATE_EMAIL)) {
                Mail::to($user->email)->send(new CodigoAccesoMail($otp));
                $this->feedbackMessage = 'Se ha enviado un código a tu email.';
            } else {
                $telefono = $user->productor->telefono ?? null;

                if ($telefono) {
                    $smsService->sendSms($telefono, "Su código de acceso es: {$otp}");
                    $this->feedbackMessage = 'Se ha enviado un código a tu teléfono.';
                } else {
                    $this->addError('identificador', 'No se pudo determinar el número de teléfono para este productor.');
                    $this->dispatchBrowserEvent('loading-finished');
                    return;
                }
            }

            $this->step = 2;
        } else {
            $this->addError('identificador', 'No se encontró un productor con ese identificador.');
        }

        $this->dispatchBrowserEvent('loading-finished');
    }

    public function iniciarSesion()
    {
        $this->validate([
            'identificador' => 'required|string',
            'codigo' => 'required|numeric|digits:6',
        ]);

        $user = $this->findUserByIdentificador($this->identificador);

        if (!$user || !Cache::has('codigo_acceso_' . $user->id) || Cache::get('codigo_acceso_' . $user->id) != $this->codigo) {
            $this->addError('codigo', 'El código de acceso es incorrecto o ha expirado.');
            return;
        }

        Cache::forget('codigo_acceso_' . $user->id);
        Auth::login($user, true); // true para "recordar" al usuario

        return redirect()->intended(config('fortify.home'));
    }

    private function findUserByIdentificador($identificador)
    {
        // Búsqueda por email
        if (filter_var($identificador, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $identificador)->first();
            // Asegurarse que el usuario sea un productor
            return ($user && $user->hasRole('productor')) ? $user : null;
        }

        // Sanitiza el identificador de entrada para que solo contenga dígitos.
        $sanitizedIdentificador = preg_replace('/[^0-9]/', '', $identificador);
        if (empty($sanitizedIdentificador)) {
            return null;
        }

        // Busca en Productores, sanitizando también el número de la BD.
        $productor = Productor::whereRaw('REPLACE(REPLACE(REPLACE(telefono, "+", ""), "-", ""), " ", "") = ?', [$sanitizedIdentificador])->first();

        // Si se encuentra un productor, devuelve su usuario asociado (la relación es 'usuario')
        if ($productor) {
            return $productor->usuario;
        }

        return null;
    }

    public function render()
    {
        return view('livewire.auth.login-otp');
    }
}
