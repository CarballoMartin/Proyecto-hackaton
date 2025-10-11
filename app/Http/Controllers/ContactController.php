<?php

namespace App\Http\Controllers;

use App\Events\ContactMessageSent;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        ContactMessageSent::dispatch($data);

        return back()->with('success', '¡Gracias por tu mensaje! Te contactaremos pronto.');
    }
}
