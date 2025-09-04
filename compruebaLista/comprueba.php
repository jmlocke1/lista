<?php
include "compruebalista/funciones.php";
$archivo_error = "h:/testear/logs/error.txt";
$archivo_bueno = "h:/testear/logs/bueno.txt";
$iniciar = "m:/Cómics";
$backup = "d:/Cómics";
$make_backup = true;

$nombre_archivo = "m:/Cómics/Baguña Hermanos/Junior Films - Baguña Hermanos S. L. (1946-1948)/Junior films (Revista)(Ed.Baguña Hermanos)(1946) [Completo][Biblioteca Artium][CRG][lamansion-crg.net].tar";
//$salida = shell_exec("tar -tvf " . escapeshellarg($nombre_archivo) . " --force-local 2>&1");
$salida = shell_exec("tar -tvf \"" . $nombre_archivo . "\" --force-local 2>&1");
var_dump($salida);
die();


$testear = new Testear(
    $archivo_bueno,
    $archivo_error,
    $iniciar,
    $backup,
    $make_backup
);

$cambio = $testear->creaDir("m:/Cómics/Editorial Bruguera/Capitán Trueno/El Capitán Trueno - Bruguera (1986)/Capitan_Trueno_Edic_86_(COMPLETO)_mInInA_[crg].rar");
var_dump($cambio);
die();

scan(scandir($iniciar), $iniciar, $archivo_bueno, $archivo_error);

class Testear {
    
    public function __construct(
        public string $archivo_bueno,
        public string $archivo_error,
        public string $iniciar,
        public string $backup,
        public string $make_backup,
    ){}

    public function scan(array $scandir){
        var_dump($scandir);
    }


    
    public function creaDir(string $nombre_archivo): string{
        $info_archivo = new SplFileInfo($nombre_archivo);
        $path = $info_archivo->getPath();
        $path = substr($path, strlen($this->iniciar));  // Le quitamos el directorio de inicio
        $path = $this->backup . $path; // Le añadimos el directorio raíz de backup
        if(!file_exists($path)) mkdir($path, 0770, true);
        return $path;
    }

    /**
     * Función que testea los ficheros rar y cbr.
     * Hay un caso especial, que es cuando el fichero no es un rar. Hay ocasiones en que los ficheros están como cbr, pero realmente son zips
     */
    public function testRar($nombre_archivo) {
        $salida = shell_exec("unrar t \"" . $nombre_archivo . "\" 2>&1"); // Hay que escapar las dobles comillas y evitar poner escapeshellarg para que funcione bien
        if(str_contains($salida, "Todo correcto")) {
            file_put_contents($this->archivo_bueno, $nombre_archivo . "\n", FILE_APPEND);
        } else {
            file_put_contents($this->archivo_error, $nombre_archivo . "\n" . $salida . "\n", FILE_APPEND);
            // Creamos el directorio
            creaDir($nombre_archivo);
        }
    }

    function testZip($nombre_archivo, $archivo_bueno, $archivo_error) {
        $salida = shell_exec("unzip -tq " . escapeshellarg($nombre_archivo) . " 2>&1");
        if(str_contains($salida, "At least one error was detected")) {
            file_put_contents($this->archivo_error, $nombre_archivo . "\n" . $salida . "\n", FILE_APPEND);
            // Creamos el directorio
            creaDir($nombre_archivo);
        } else {
            file_put_contents($this->archivo_bueno, $nombre_archivo . "\n", FILE_APPEND);
        }
    }
}