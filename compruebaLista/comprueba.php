<?php
include "compruebalista/funciones.php";
$archivo_error = "h:/testear/logs/error.txt";
$archivo_bueno = "h:/testear/logs/bueno.txt";
$iniciar = "d:/Cómics";

scan(scandir($iniciar), $iniciar, $archivo_bueno, $archivo_error);
