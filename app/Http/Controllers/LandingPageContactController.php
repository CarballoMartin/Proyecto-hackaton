<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Mail\ContactFormMail;

class LandingPageContactController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        try {
            Mail::to(config('mail.from.address'))->send(new ContactFormMail($validatedData));
            return response()->json(['message' => 'Mensaje enviado con éxito.']);
        } catch (\Exception $e) {
            Log::error('Error sending contact form email: ' . $e->getMessage());
            return response()->json(['message' => 'Hubo un problema al enviar tu mensaje. Por favor, inténtalo de nuevo más tarde.'], 500);
        }
    }
}
