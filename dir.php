<?php
require_once "config/app.php";
use wapmorgan\Mp3Info\Mp3Info;

$comm = [
    "" => [
        "short" => "",
        "actual" => ""
    ]
];

debuguear($comm['']);
// $pru['tags'] = "valor en tags";
$pru['tags2'] = "";
$valor = !empty($pru['tags2']) ? $pru['tags2'] : (!empty($pru['tags1']) ? $pru['tags1'] : (!empty($pru['tags']) ? $pru['tags'] :  'No hay ningún valor'));
debuguear($valor);

$cd = "H:";
$dirs = scandir($cd);




// $dirH = scan($dirs, $cd);
// debuguear($dirH);
$foto = $cd."/".$dirs[1]."/1_Duke Ellington & His Orchestra - Mood Indigo.mp3";
$informacion = new Mp3Info($foto, true);
if(isMusic($foto)){
    echo "Es un fichero mp3";
}
//debuguear($informacion);