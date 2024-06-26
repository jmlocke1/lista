<?php
require_once __DIR__."/../config/app.php";

use App\config\Config;
use App\helpers\Mp3InfoHelper;
use Model\ActiveRecord;
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
 * Función que devuelve la ruta de una imagen diferente cada día del año.
 * La imagen se va rotando en función de la cantidad de imágenes que haya
 *
 * @param string $type
 * @param integer $offset
 * @return string
 */
function imageOfTheDay($type = 'jpg', $offset = 0): string {
	$offset = getIndexOfScript();
	if(!in_array($type, Config::IMAGE_TYPES)) $type = 'jpg';
	$dir = dir(Config::ABSOLUTE_DIR_IMG_PRINCIPAL);
	$images = [];
	while (false !== $entry = $dir->read()) {
		$img = explode('.', $entry);
		if(end($img) === $type){
			$images[] = Config::DIR_IMG_PRINCIPAL . $entry;
		}
	}
	$max = count($images);
	$day = (int) date('z');
	$index = ($day + $offset) % $max;
	return $images[$index];
}

function getIndexOfScript() {
	$offset = 0;
	$script = $_SERVER['REQUEST_URI'];
	$index = array_search($script, Config::PAGES);
	if(is_int($index)){
		$offset = $index;
	}
	return $offset;
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

/**
 * Función que comprueba si el array pasado es asociativo o indexado
 *
 * @param array $arr
 * @return boolean | null   True si es asociativo, falso si no, null si está vacío
 */
function isAssociativeArray(array $arr): bool | null{
    if(empty($arr))  return null;
    return array_keys($arr) !== range(0, count($arr) - 1);
}

/**
 * Elimina los espacios en blanco al principio y al final en todos los valores 
 * del array pasado como parámetro
 *
 * @param array $arr
 * @return array
 */
function trim_array(array $arr): array{
    foreach ($arr as $key => $value) {
        $arr[$key] = trim($value);
    }
    return $arr;
}

/**
 * Envía al cliente los mensajes de alerta
 * 
 * @param bool $result      Booleano que indica si la operación ha resultado bien o mal
 */
function sendAlerts(bool $result){
    $response['result'] = $result;
    $response['alerts'] = ActiveRecord::getAlerts();
    echo json_encode($response);
}

/**
 * Convierte una url en un array de nodos<br>.
 * Por ejemplo, si se introduce la url /blog/prueba/destino/ creará el siguiente array:
 * ['blog', 'prueba', 'destino']<p>
 * Si se usara directamente la función explode con esa url crearía el siguiente array:
 * ['', 'blog', 'prueba', 'destino', '']
 * O sea, las barras de directorio inicial y final las interpretaría como directorios y les asigna una cadena vacía
 *
 * @param string $url   Url a convertir en array
 * @return array        Array con todos los nodos de la url
 */
function urlToArray(string $url): array{
    $urlTemp = explode('/', $url);
    $urlFinal = [];
    // Eliminamos los nodos vacíos
    foreach($urlTemp as $node){
        if($node) $urlFinal[] = $node;
    }
    return $urlFinal;
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
            $html .= "<li>Folder Name: {$dir}";
            $html .= "<ul>";
            $html .= "<li>artist: </li>";

            $html .= "<ul>".listaHtml($possibleFile, $root."/".$dir)."</ul>";
            $html .= "</ul>";
            $html .= "</li>";
    
        }else{
            $html .= "<li>{$possibleFile}";
            $html .= infoSongHtml($root."/".$possibleFile);
            $html .= "</li>";
        }
    }
    return $html;
}

function infoSongHtml($song){
    try {
        $info = new Mp3Info($song, true);
        
        $infoHelp = new Mp3InfoHelper($info);
    } catch (\Throwable $th) {
        echo $th->getMessage();
        return;
    }
    
    // debuguearSinExit($info);
    
    $html = "<ul>";
    $html .= "<li>codecVersion: {$info->codecVersion}</li>";
    $html .= "<li>layerVersion: {$info->layerVersion}</li>";
    $html .= "<li>audioSize: {$info->audioSize}</li>";
    // $html .= "<li>filesize: ".filesize($song)."</li>";
    $html .= "<li>duration: {$info->duration}</li>";
    $html .= "<li>bitRate: {$info->bitRate}</li>";
    $html .= "<li>sampleRate: {$info->sampleRate}</li>";
    $html .= "<li>hasCover: ".($info->hasCover ? 'true' : 'false')."</li>";
    $html .= "<li>channel: ".($info->channel)."</li>";
    
    $html .= "<li>song: ".($infoHelp->song)."</li>";
    $html .= "<li>artist: ".($infoHelp->artist)."</li>";
    $html .= "<li title='The ‘Band/Orchestra/Accompaniment’ frame is used for additional information about the performers in the recording'>tpe2: ".($infoHelp->tpe2)."</li>";
    $html .= "<li title='The ‘Conductor’ frame is used for the name of the conductor'>tpe3: ".($infoHelp->tpe3)."</li>";
    $html .= "<li title='The ‘Interpreted, remixed, or otherwise modified by’ frame contains more information about the people behind a remix and similar interpretations of another existing piece'>tpe4: ".($infoHelp->tpe4)."</li>";
    $html .= "<li title='The ‘Original artist/performer’ frame is intended for the performer of the original recording, if for example the music in the file should be a cover of a previously released song.'>tope: ".($infoHelp->tope)."</li>";
    $html .= "<li>album: ".($infoHelp->album)."</li>";
    $html .= "<li>composer: ".($infoHelp->composer)."</li>";
    $html .= "<li>year: ".($infoHelp->year)."</li>";
    $html .= "<li>comment: ".($infoHelp->comment)."</li>";
    $html .= "<li>track: ".($infoHelp->track)."</li>";
    $html .= "<li>genrev1: ".($infoHelp->genrev1)."</li>";
    $html .= "<li>genrev2: ".($infoHelp->genrev2)."</li>";
    $html .= "</ul>";
    return $html;
}