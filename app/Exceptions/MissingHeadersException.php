<?php

namespace App\Exceptions;

use InvalidArgumentException;

class MissingHeadersException extends InvalidArgumentException
{
    public array $missingHeaders;

    public function __construct($missingHeaders, $message = "", $code = 0, \Throwable $previous = null)
    {
        $this->missingHeaders = $missingHeaders;
        if (empty($message)) {
            $message = "Faltan las siguientes columnas obligatorias: " . implode(', ', $this->missingHeaders);
        }
        parent::__construct($message, $code, $previous);
    }
}
