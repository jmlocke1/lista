<?php

// Ruta al archivo RAR que quieres comprobar
$rutaArchivoRar = 'aristogatos.cbr';

// Verifica si el archivo RAR existe
if (!file_exists($rutaArchivoRar)) {
    die("El archivo RAR no se encuentra en la ruta especificada.");
}

// Intenta abrir el archivo RAR
try {
    $archivoRar = RarArchive::open($rutaArchivoRar);

    // Verifica si el archivo está roto (dañado o incompleto)
    if ($archivoRar->isBroken()) {
        echo "El archivo RAR está dañado o incompleto.";
    } else {
        echo "El archivo RAR parece ser válido.";
        // Opcional: puedes listar las entradas para una comprobación más profunda
        // $entradas = $archivoRar->getEntries();
        // echo "Contiene " . count($entradas) . " entradas.";
    }

    // Cierra el archivo RAR liberando los recursos
    $archivoRar->close();

} catch (RarException $e) {
    echo "Error al procesar el archivo RAR: " . $e->getMessage();
}

?>