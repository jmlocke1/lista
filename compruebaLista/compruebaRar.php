<?php

include "compruebalista/funciones.php";
include "compruebaLista/cbr.php";

$locale = 'es_ES.UTF-8';
setlocale(LC_ALL, $locale);
putenv('LC_ALL='.$locale);
$nombre_archivo = "M:/Cómics/Comics Eróticos/[Oh! Great] Seikimaru [Spanish].cbr";
$salida = shell_exec("unrar t \"" . $nombre_archivo . "\" 2>&1");
var_dump($salida);
die();
$locale = 'es_ES.UTF-8';
setlocale(LC_ALL, $locale);
putenv('LC_ALL='.$locale);
for($i = 0, $limit = count($ficheros); $i < $limit; $i++){
    $nombre_archivo = $ficheros[$i];
    $salida = shell_exec("unrar t " . escapeshellarg($nombre_archivo) . " 2>&1");
    if(str_contains($salida, "Todo correcto")) {
        file_put_contents($archivo_bueno, $nombre_archivo . "\n", FILE_APPEND);
    } else {
        file_put_contents($archivo_error, $nombre_archivo . "\n" . $salida . "\n", FILE_APPEND);
        // Creamos el directorio
        creaDir($nombre_archivo);
    }
}


// for($i = 0, $limit = count($ficheros); $i < $limit; $i++){
//     $nombre_archivo = $ficheros[$i];
//     testRar($nombre_archivo, $archivo_bueno, $archivo_error);
// }
die();
// Ruta al archivo RAR que quieres comprobar
$rutaArchivoRar = "H:/Cómics/Comics Eroticos/error-dios-ama.cbr";

// Verifica si el archivo RAR existe
if (!file_exists($rutaArchivoRar)) {
    die("El archivo RAR no se encuentra en la ruta especificada.");
}
RarException::setUsingExceptions(true);
// Intenta abrir el archivo RAR
try {
    echo escapeshellarg($rutaArchivoRar) . "\n";
    echo $rutaArchivoRar . "\n";
    $archivoRar = RarArchive::open($rutaArchivoRar);

    // Verifica si el archivo está roto (dañado o incompleto)
    if ($archivoRar->isBroken()) {
        echo "El archivo RAR está dañado o incompleto.";
    } else {
        echo "El archivo RAR parece ser válido.";
        // Opcional: puedes listar las entradas para una comprobación más profunda
        // $entradas = $archivoRar->getEntries();
        // echo "Contiene " . count($entradas) . " entradas.";
        // var_dump($entradas);
    }

    // Cierra el archivo RAR liberando los recursos
    $archivoRar->close();

} catch (RarException $e) {
    echo "Error al procesar el archivo RAR: " . $e->getMessage();
} catch (Exception $e) {
    echo "Error general al procesar archivo rar: " . $e->getMessage();
}

?>