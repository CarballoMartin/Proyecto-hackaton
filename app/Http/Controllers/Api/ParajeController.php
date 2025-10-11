<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Municipio;
use Illuminate\Http\Request;

class ParajeController extends Controller
{
    public function index(Municipio $municipio)
    {
        return response()->json($municipio->parajes()->orderBy('nombre')->get());
    }
}
