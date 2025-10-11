<?php

namespace App\Http\Controllers\Institucional;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // ... (existing methods)

    public function panelMaqueta(Request $request)
    {
        // This method is just for showing the mockup
        $institucionNombre = 'Instituto Nacional de Tecnología Agropecuaria (INTA)';

        // Mock data for the cards
        $stats = [
            'productores' => 152,
            'participantes' => 12,
            'solicitudes' => 3,
        ];

        return view('institucional.panel-maqueta', [
            'institucionNombre' => $institucionNombre,
            'stats' => $stats,
        ]);
    }
}