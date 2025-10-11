<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Log as AppLog;
use App\Models\User;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = AppLog::with('user:id,name')->latest();

        // Filtrar por rol de usuario
        if ($request->has('rol') && $request->rol) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('rol', $request->rol);
            });
        }

        // Filtrar por usuario específico
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filtrar por entidad afectada
        if ($request->has('modelo') && $request->modelo) {
            $query->where('modelo', $request->modelo);
        }
        if ($request->has('modelo_id') && $request->modelo_id) {
            $query->where('modelo_id', $request->modelo_id);
        }

        // Filtrar por rango de fechas
        if ($request->has('fecha_inicio') && $request->fecha_inicio) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->has('fecha_fin') && $request->fecha_fin) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        $logs = $query->paginate(70);

        return response()->json($logs);
    }

    public function getUsers(Request $request)
    {
        $query = User::query()->select('id', 'name', 'rol')->orderBy('name');

        if ($request->has('rol') && $request->rol) {
            $query->where('rol', $request->rol);
        }

        $users = $query->get();

        return response()->json($users);
    }
}