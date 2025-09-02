<?php
include "compruebalista/funciones.php";
include "compruebaLista/cbz.php";


for($i = 0, $limit = count($ficheros); $i < $limit; $i++){
    $nombre_archivo = $ficheros[$i];
    $salida = shell_exec("unzip -tq " . escapeshellarg($nombre_archivo) . " 2>&1");
    if(str_contains($salida, "At least one error was detected")) {
        file_put_contents($archivo_error, $nombre_archivo . "\n" . $salida . "\n", FILE_APPEND);
        // Creamos el directorio
        creaDir($nombre_archivo);
    } else {
        file_put_contents($archivo_bueno, $nombre_archivo . "\n", FILE_APPEND);
    }
}

die();
$ruta_zip = 'h:/testear/errorBlackPanther012 (2010) (Minutemen-DarthTremens).cbz';
$extraer = 'h:/testear/extraer/';

if (file_exists($ruta_zip)) {
    echo "El archivo ZIP existe.\n";
    $extension = pathinfo($ruta_zip, PATHINFO_EXTENSION);
    echo "La extensión es: " . $extension . "\n";
    // Proceder a la verificación del formato ZIP
} else {
    echo "Error: El archivo ZIP no se encontró.\n";
}


$zip = new ZipArchive();
$res = $zip->open($ruta_zip);

if ($res === TRUE) {
    echo "El archivo ZIP está bien formado y se abrió correctamente.\n";
    $exito = $zip->extractTo($extraer, array($zip->getNameIndex(1)));
    if($exito === TRUE) {
        echo "Se han extraído correctamente todos los archivos\n";
    }else{
        echo "Ha ocurrido un fallo al extraer";
    }
    echo "<br>Número de archivos dentro del ZIP: " . $zip->numFiles . "<br>";

    // Puedes listar los archivos o extraerlos
    // for ($i = 0; $i < $zip->numFiles; $i++) {
    //     echo " - " . $zip->getNameIndex($i) . "\n";
    // }

    $zip->close(); // Cierra el archivo ZIP
} else {
    echo "Error: No se pudo abrir el archivo ZIP. Puede estar corrupto o no ser un archivo ZIP válido. Código de error: " . $res . "<br>";
}