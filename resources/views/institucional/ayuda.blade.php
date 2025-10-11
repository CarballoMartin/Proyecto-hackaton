@extends('layouts.institucional')

@section('title', 'Ayuda e Instructivos')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-3xl shadow-xl p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Ayuda e Instructivos</h1>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-6">
                    <div class="bg-blue-50 rounded-2xl p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">📋 Guías Rápidas</h2>
                        <ul class="space-y-2 text-gray-700">
                            <li>• Cómo gestionar participantes</li>
                            <li>• Proceso de verificación</li>
                            <li>• Generar reportes</li>
                            <li>• Configurar notificaciones</li>
                        </ul>
                    </div>

                    <div class="bg-green-50 rounded-2xl p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">🔧 Solución de Problemas</h2>
                        <ul class="space-y-2 text-gray-700">
                            <li>• No puedo acceder al sistema</li>
                            <li>• Error al cargar datos</li>
                            <li>• Problemas con notificaciones</li>
                            <li>• Restablecer contraseña</li>
                        </ul>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-yellow-50 rounded-2xl p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">📞 Contacto de Soporte</h2>
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3">
                                <span class="text-2xl">📧</span>
                                <div>
                                    <p class="font-medium text-gray-900">Email</p>
                                    <p class="text-sm text-gray-600">soporte@sistema.com</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="text-2xl">📞</span>
                                <div>
                                    <p class="font-medium text-gray-900">Teléfono</p>
                                    <p class="text-sm text-gray-600">+1 (555) 123-4567</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="text-2xl">🕒</span>
                                <div>
                                    <p class="font-medium text-gray-900">Horario</p>
                                    <p class="text-sm text-gray-600">Lun-Vie: 9:00-18:00</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-purple-50 rounded-2xl p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">📚 Recursos Adicionales</h2>
                        <ul class="space-y-2 text-gray-700">
                            <li>• Manual de usuario completo</li>
                            <li>• Videos tutoriales</li>
                            <li>• Preguntas frecuentes</li>
                            <li>• Base de conocimientos</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




















