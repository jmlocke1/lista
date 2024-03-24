<?php
use wapmorgan\Mp3Info\Mp3Info;

/**
 * Función simple que ayuda a depurar programas. No sustituye a un debug, pero puede
 * ser mucho más rápido. Imprime la variable a comprobar y corta la ejecución
 *
 * @param [type] $variable
 * @return void
 */
function debuguear($variable) {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

/**
 * Función simple que ayuda a depurar programas. No sustituye a un debug, pero puede
 * ser mucho más rápido. Imprime la variable a comprobar pero no corta la ejecución
 *
 * @param [type] $variable
 * @return void
 */
function debuguearSinExit($variable) {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
}

/**
 * Función que recibe un nombre compuesto por palabras separadas por
 * guión bajo y devuelve una palabra con set y el resto en camelcase.
 * Por ejemplo, si tenemos un campo llamado numeric_code_patogenic_militar,
 * la función devolverá setNumericCodePatogenicMilitar
 * 
 * Se usa para obtener el nombre de las funciones set en los modelos
 *
 * @param string $field
 * @return string
 */
function nombreSetFunction(string $field): string {
	$final = "set" . ucfirst($field);
	while($pos = strpos($final, '_')) {
		$final = substr($final, 0, $pos) . ucfirst(substr($final, $pos + 1));
	}
	return $final;
}

function scan(array $dirs, string $root, array $tree = []){
    foreach($dirs as $dir){
        if(($dir === ".") || ($dir === "..")) continue;
        $possibleFile = $root."/".$dir;
        if(is_dir($possibleFile)){
            $newScan = scan(scandir($possibleFile), $possibleFile);
            if(!empty($newScan)) $tree["{$dir}"] = $newScan;
        }else{
            if(isMusic($dir)) $tree[] = $dir;
        }
    }
    return $tree;
}

function isMusic($file){
    $fileInfo = new SplFileInfo($file);
    return strtolower($fileInfo->getExtension()) === "mp3";
}

function listaHtml($fileInfo, $root){
    $html = "";
    foreach ($fileInfo as $dir => $possibleFile) {
        if(is_array($possibleFile)){
            $html .= "<li>{$dir}";
            $html .= "<ul>".listaHtml($possibleFile, $root."/".$dir)."</ul>";
            $html .= "</li>";
        }else{
            $html .= "<li>{$dir} - {$possibleFile}";
            $html .= infoSongHtml($root."/",$possibleFile);
            $html .= "</li>";
        }
    }
    return $html;
}

function infoSongHtml($song){
    $info = new Mp3Info($song, true);
    $html = "<ul>";
    $html .= "<li></li>";
    $html .= "</ul>";
    return $html;
}