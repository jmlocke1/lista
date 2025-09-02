<?php
include "compruebalista/funciones.php";
include "compruebaLista/tar.php";

for($i = 0, $limit = count($ficheros); $i < $limit; $i++){
    $nombre_archivo = $ficheros[$i];
    $salida = shell_exec("tar -tvf " . escapeshellarg($nombre_archivo) . " --force-local 2>&1");
    if(str_contains($salida, "Exiting with failure status due to previous errors")) {
        file_put_contents($archivo_error, $nombre_archivo . "\n" . $salida . "\n", FILE_APPEND);
        // Creamos el directorio
        creaDir($nombre_archivo);
    } else {
        file_put_contents($archivo_bueno, $nombre_archivo . "\n", FILE_APPEND);
    }
}

