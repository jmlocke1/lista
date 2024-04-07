<?php 
require_once "config/app.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Formulario de música</h1>

    <form action="mp3-save.php" method="post">
        <fieldset>
            <legend>Album: The Great Paris Concert CD1</legend>

            <label for="album[0]['nombre']">Nombre Album</label>
            <input type="text" name="album[0]['nombre']" value="The Great Paris Concert CD1">
            <label for="album[0]['nombre']">Subtítulo Album</label>
            <input type="text" name="album[0]['subtitle']" value="">
            <?php 
            $published = date('Y-m-d', strtotime('1973-01-01'));
            ?>
            <label for="album[0]['year']">Fecha de publicación</label>
            <input type="date" name="album[0]['year']" value="<?= $published; ?>">
            <label for="album[0]['folder']">Directorio</label>
            <input size="50" type="text" name="album[0]['folder']" value="1963 - The Great Paris Concert CD1">

        </fieldset>
    </form>
</body>
</html>