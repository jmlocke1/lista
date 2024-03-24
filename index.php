<?php
require_once "config/app.php";

use wapmorgan\Mp3Info\Mp3Info;

$rutaDeLaCancion = "H:/1950 - Masterpieces/1_Duke Ellington & His Orchestra - Mood Indigo.mp3";

$informacion = new Mp3Info($rutaDeLaCancion, true);
$tags1 = $informacion->tags1;
$tags2 = $informacion->tags2;
var_dump($tags1);
var_dump($tags2);

$peso = $informacion->_fileSize;
$duracionEnSegundos = $informacion->duration;
$bitRate = $informacion->bitRate;
$canal = $informacion->channel;
printf("El peso es %f, la duración en segundos es %f, el bitrate es de %d bps y el canal es %s",
$peso, $duracionEnSegundos, $bitRate, $canal);