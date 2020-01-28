<?php
namespace App\Service;
class Languages
{
    public $langs;

    // Constructor con las variables iniciales
    public function __construct() {
        // Lenguajes disponibles en la aplicación
        $this->langs = ['es','en'];
    }

    public function getLangs() {
        // Lenguajes disponibles en la aplicación
        return $this->langs;
    }
}