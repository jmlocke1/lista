<?php 

if(!isset($title)) $title = 'Lista';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?></title>
<?php if(isset($cssPersonal)){ 
    echo $cssPersonal;
} ?>

    <link rel="stylesheet" href="/build/css/app.css">
<?php if(isset($javascriptPersonal)){ 
    echo $javascriptPersonal;
} ?>

<?php if(isset($jsview)){ ?>
    <script type="module" src="/build/js/views/<?= $jsview; ?>.js"></script>
<?php } ?>

</head>
<body>
    <div class="contenedor">
        <div class="barra">
            <nav class="navegacion">
                <a href="/" class="navegacion__enlace">Programas</a>
                <a href="/musica" class="navegacion__enlace">Música</a>
            </nav>
        </div>
    </div>