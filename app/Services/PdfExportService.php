<?php

namespace App\Services;

use App\Interfaces\PdfExportServiceInterface;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfExportService implements PdfExportServiceInterface
{
    /**
     * Genera un PDF a partir de una vista de Blade y lo ofrece para descarga.
     *
     * @param string $view Nombre de la vista de Blade.
     * @param array $data Datos para pasar a la vista.
     * @param string $filename Nombre del archivo para la descarga.
     * @return \Illuminate\Http\Response
     */
    public function generateFromView(string $view, array $data, string $filename = 'documento.pdf')
    {
        $pdf = Pdf::loadView($view, $data);
        
        // Opcional: se pueden configurar opciones de dompdf aquí
        // $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download($filename);
    }
}
