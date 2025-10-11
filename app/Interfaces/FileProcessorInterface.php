<?php

namespace App\Interfaces;

use Illuminate\Support\Collection;

interface FileProcessorInterface
{
    /**
     * Procesa un archivo desde su ruta y lo convierte en una Colección.
     *
     * @param string $filePath La ruta absoluta al archivo.
     * @param string|null $originalName El nombre original del archivo (para la extensión).
     * @return Collection<array>
     */
    public function process(string $filePath, ?string $originalName = null): Collection;
}