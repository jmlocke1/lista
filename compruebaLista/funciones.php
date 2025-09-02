<?php


function creaDir(string $nombre_archivo){
    $info_archivo = new SplFileInfo($nombre_archivo);
    $path = $info_archivo->getPath();
    $path[0] = "h";
    if(!file_exists($path)) mkdir($path, 0770, true);
}


// function testRar($nombre_archivo, $archivo_bueno, $archivo_error) {
//     RarException::setUsingExceptions(true);
//     // Intenta abrir el archivo RAR
//     try {
//         $archivoRar = RarArchive::open($nombre_archivo);

//         // Verifica si el archivo está roto (dañado o incompleto)
//         if ($archivoRar->isBroken()) {
//             file_put_contents($archivo_error, $nombre_archivo . " - El archivo RAR está dañado o incompleto.\n", FILE_APPEND);
//             // Creamos el directorio
//             creaDir($nombre_archivo);
//         } else {
//             file_put_contents($archivo_bueno, $nombre_archivo . "\n", FILE_APPEND);
//         }

//         // Cierra el archivo RAR liberando los recursos
//         $archivoRar->close();

//     } catch (RarException $e) {
//         file_put_contents($archivo_error, $nombre_archivo . "\n    - Error al procesar el archivo RAR: " . $e->getMessage() . "\n\n", FILE_APPEND);
//         // Creamos el directorio
//         creaDir($nombre_archivo);
//     } catch (Exception $e) {
//         file_put_contents($archivo_error, $nombre_archivo . "\n    - Error general al procesar archivo rar: " . $e->getMessage() . "\n\n", FILE_APPEND);
//         // Creamos el directorio
//         creaDir($nombre_archivo);
//     }
// }

/**
 * Función que testea los ficheros rar y cbr.
 * Hay un caso especial, que es cuando el fichero no es un rar. Hay ocasiones en que los ficheros están como cbr, pero realmente son zips
 */
function testRar($nombre_archivo, $archivo_bueno, $archivo_error) {
    $salida = shell_exec("unrar t \"" . $nombre_archivo . "\" 2>&1"); // Hay que escapar las dobles comillas y evitar poner escapeshellarg para que funcione bien
    if(str_contains($salida, "Todo correcto")) {
        file_put_contents($archivo_bueno, $nombre_archivo . "\n", FILE_APPEND);
    } else {
        file_put_contents($archivo_error, $nombre_archivo . "\n" . $salida . "\n", FILE_APPEND);
        // Creamos el directorio
        creaDir($nombre_archivo);
    }
}

function testZip($nombre_archivo, $archivo_bueno, $archivo_error) {
    $salida = shell_exec("unzip -tq " . escapeshellarg($nombre_archivo) . " 2>&1");
    if(str_contains($salida, "At least one error was detected")) {
        file_put_contents($archivo_error, $nombre_archivo . "\n" . $salida . "\n", FILE_APPEND);
        // Creamos el directorio
        creaDir($nombre_archivo);
    } else {
        file_put_contents($archivo_bueno, $nombre_archivo . "\n", FILE_APPEND);
    }
}

function testTar($nombre_archivo, $archivo_bueno, $archivo_error) {
    $salida = shell_exec("tar -tvf " . escapeshellarg($nombre_archivo) . " --force-local 2>&1");
    if(str_contains($salida, "Exiting with failure status due to previous errors")) {
        file_put_contents($archivo_error, $nombre_archivo . "\n" . $salida . "\n", FILE_APPEND);
        // Creamos el directorio
        creaDir($nombre_archivo);
    } else {
        file_put_contents($archivo_bueno, $nombre_archivo . "\n", FILE_APPEND);
    }
}

/**
 * 
 */
function scan(array $dirs, string $root, string $archivo_bueno, string $archivo_error): void {
    foreach($dirs as $dir){
        if(($dir === ".") || ($dir === "..")) continue;
        $possibleFile = $root."/".$dir;
        if(is_dir($possibleFile)){
            scan(scandir($possibleFile), $possibleFile, $archivo_bueno, $archivo_error);
        }else{
            // Es un fichero, comprobemos su extensión
            $ext = strtolower(pathinfo($possibleFile, PATHINFO_EXTENSION));
            if($ext === "rar" || $ext === "cbr"){
                testRar($possibleFile, $archivo_bueno, $archivo_error);
            }
            if($ext === "zip" || $ext === "cbz"){
                testZip($possibleFile, $archivo_bueno, $archivo_error);
            }
            if($ext === "tar"){
                testTar($possibleFile, $archivo_bueno, $archivo_error);
            }
        }
    }
}