<?php

namespace App\Interfaces;

interface PdfExportServiceInterface
{
    /**
     * Genera un PDF a partir de una vista de Blade y lo ofrece para descarga.
     *
     * @param string $view Nombre de la vista de Blade.
     * @param array $data Datos para pasar a la vista.
     * @param string $filename Nombre del archivo para la descarga.
     * @return \Illuminate\Http\Response
     */
    public function generateFromView(string $view, array $data, string $filename = 'documento.pdf');
}
