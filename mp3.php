<?php
require_once "config/app.php";
$cd = "H:";
$cdInfo = scan(scandir($cd), $cd);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MP3- Obtener Información</title>
</head>
<body>
    <h1>Escaneando la unidad <?= $cd; ?></h1>
    <ul>
    <?= listaHtml($cdInfo, $cd); ?>
    </ul>
</body>
</html>