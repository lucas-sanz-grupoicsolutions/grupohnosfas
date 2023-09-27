<?php

namespace App\Libraries;

use CodeIgniter\I18n\Time;

class Log
{
    public string $logFile;
    public Time $date;

    public function __construct(string $fileName = null, bool $fullPath = false)
    {
        $pathNew = WRITEPATH . '/logs/logCustom/';
        $directories = explode("/", $fileName);
        $fileName = array_pop($directories);
        $fileName = ($fileName !== "") ? $fileName : null;
        if (count($directories)) {
            $directories = implode("/", $directories);
            if ($fullPath && !is_dir($directories)) {
                mkdir($directories, 0777, true);
            } elseif (!is_dir($pathNew  . "/" . $directories)) {
                mkdir($pathNew  . "/" . $directories, 0777, true);
            }
            $directories = "/" . $directories . "/";
        } else {
            $directories = "/";
        }
        $this->date = new Time('now', new \DateTimeZone('Europe/Madrid'));
        if ($fullPath) {
            $this->logFile = $fileName . "-" . $this->date->format('Y-m-d') . ".txt";
        } elseif ($fileName === null) {
            $this->logFile = $pathNew  . $directories . "log-" . $this->date->format('Y-m-d') . ".txt";
        } else {
            $this->logFile = $pathNew  . $directories . "log-" . $fileName . "-" . $this->date->format('Y-m-d') . ".txt";
        }
        $this->initFile();
    }

    /**
     * Inicializa el archivo si no existe
     */
    private function initFile(): void
    {
        if (!\file_exists($this->logFile)) {
            $this->setLine('Fichero creado');
        }
    }

    /**
     * Escribe una linea en el archivo
     */
    public function setLine(string $cadena, $datos = null): void
    {
        if ($datos !== null) {
            $datosCadena = ' Datos: ';
            if (is_array($datos)) {
                foreach ($datos as $key => $dato) {
                    if (is_array($dato) || is_object($dato)) {
                        $dato = var_export($dato, true);
                    }
                    $datosCadena = $datosCadena . "[" . $key . "] => " . $dato . " | ";
                }
            } elseif (is_object($datos)) {
                $dato = var_export($datos, true);
                $datosCadena = $datosCadena . "[0] => " . $dato . " | ";
            } else {
                $datosCadena = $datosCadena . $datos;
            }
            $cadena = $cadena . $datosCadena;
        }
        if ($file = fopen($this->logFile, "a")) {
            fwrite($file, "[" . $this->date->format("d-m-Y H:i:s") . "] " . $cadena . "\n");
            fclose($file);
        }
    }
}