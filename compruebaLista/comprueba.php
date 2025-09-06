<?php
include "compruebalista/funciones.php";
$dir_logs = "h:/testear/logs"; // Directorio donde se guardarán los logs de salida del programa
$iniciar = "m:/Cómics";
$backup = "d:/Cómics";
// Cuando un rar es correcto, se supone que el mensaje siempre incluye un Todo correcto
$rar_msg = "Todo correcto";
// El mensaje de un zip es similar, cuando está bien, se supone que siempre dice lo mismo, pero en caso
// de error, puede variar
$zip_msg = "No errors detected";
// En el caso del tar, cuando no hay errores no dice ningún mensaje, y se supone que siempre
// dice lo mismo en caso de error, pero aún no estoy seguro. Tendré que hacer varias pruebas
$tar_msg = "Exiting with failure status due to previous errors";
$make_backup = true;


$nombre_archivo = "m:/Cómics/Antonio Ivars Portabella/Orlando Príncipe de las tinieblas - Antonio Ivars (1965)/Orlando principe de las tinieblas 25 [por Fariña].cbz";
$salida = shell_exec("unzip -tq " . escapeshellarg($nombre_archivo) . " 2>&1");
var_dump($salida);
$salida = shell_exec("unrar t \"" . $nombre_archivo . "\" 2>&1");
//  var_dump($salida);
// $nombre_archivo = "m:/Cómics/Buru Lan S.A. de Ediciones/Rip Kirby - Buru Lan 1976/Rip Kirby (Color) (Ed.Buru Lan)(1976) [Completo][por cacolus262][CRG][lamansion-crg.net].tar";
// $salida = shell_exec("tar -tvf " . escapeshellarg($nombre_archivo) . " --force-local 2>&1");
// $dir_destino = "z:/tarex";

//$salida = shell_exec("tar -xf " . escapeshellarg($nombre_archivo)  . " -C " . escapeshellarg($dir_destino) . " --force-local 2>&1");
echo "Mostrando la salida\n";
var_dump($salida);
die();


$testear = new Testear(
    $dir_logs,
    $iniciar,
    $backup,
    $make_backup,
    $rar_msg,
    $zip_msg,
    $tar_msg
);

$cambio = $testear->creaDir("m:/Cómics/Editorial Bruguera/Capitán Trueno/El Capitán Trueno - Bruguera (1986)/Capitan_Trueno_Edic_86_(COMPLETO)_mInInA_[crg].rar");
var_dump($cambio);
die();

scan(scandir($iniciar), $iniciar, $archivo_bueno, $archivo_error);

class Testear {
    public string $archivo_bueno;
    public string $archivo_error;
    public string $zip_rar_cambiados;
    public function __construct(
        public string $dir_logs,
        public string $iniciar,
        public string $backup,
        public bool $make_backup,
        public string $rar_msg,
        public string $zip_msg,
        public string $tar_msg
    ){
        if(!file_exists($dir_logs)) mkdir($dir_logs, 0770, true);
        $time = time();
        $this->archivo_bueno = $dir_logs . "/bueno" . $time . ".txt";
        $this->archivo_error = $dir_logs . "/error" . $time . ".txt";
        $this->zip_rar_cambiados = $dir_logs . "/zip_rar_cambiados" . $time . ".txt";
    }

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
        if(str_contains($salida, $this->rar_msg)) {
            file_put_contents($this->archivo_bueno, $nombre_archivo . "\n", FILE_APPEND);
        } else {
            file_put_contents($this->archivo_error, $nombre_archivo . "\n" . $salida . "\n", FILE_APPEND);
            // Creamos el directorio, si procede
            if($this->make_backup) creaDir($nombre_archivo);
        }
    }

    function testZip($nombre_archivo) {
        $salida = shell_exec("unzip -tq " . escapeshellarg($nombre_archivo) . " 2>&1");
        if(str_contains($salida, $this->zip_msg)) {
            file_put_contents($this->archivo_bueno, $nombre_archivo . "\n", FILE_APPEND);
        } else {
            file_put_contents($this->archivo_error, $nombre_archivo . "\n" . $salida . "\n", FILE_APPEND);
            // Creamos el directorio
            if($this->make_backup) creaDir($nombre_archivo);
        }
    }

    function testTar($nombre_archivo) {
        $salida = shell_exec("tar -tvf " . escapeshellarg($nombre_archivo) . " --force-local 2>&1");
        if(str_contains($salida, $this->tar_msg)) {
            file_put_contents($this->archivo_error, $nombre_archivo . "\n" . $salida . "\n", FILE_APPEND);
            // Creamos el directorio
            if($this->make_backup) creaDir($nombre_archivo);
        } else {
            file_put_contents($this->archivo_bueno, $nombre_archivo . "\n", FILE_APPEND);
        }
    }

    public function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return true;
        }
        if (!is_dir($dir)) {
            return unlink($dir);
        }
        
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        return rmdir($dir);
    }

}