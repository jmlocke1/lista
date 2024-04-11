<?php
require_once "config/app.php";

use Model\ActiveRecord;
use Model\Album;


$tabla = ActiveRecord::describeTable('track');
debuguear($tabla);
$albumData = $_POST['album'][0];
debuguearSinExit($albumData);
$album = new Album();
$album->num_ubicacion = $albumData['num_ubicacion'] + 0;
debuguearSinExit($album);
debuguear($_POST);