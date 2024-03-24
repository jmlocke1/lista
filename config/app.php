<?php
// Definimos el directorio padre del sistema
define("DIR_ROOT", dirname(__DIR__));
// Activamos el autoloader de Composer
require DIR_ROOT.'/vendor/autoload.php';
// Cargamos el fichero de configuración y funciones
// require_once __DIR__.'/Config.php';
require_once DIR_ROOT.'/util/funciones.php';