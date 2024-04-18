<?php 

if(!isset($title)) $title = 'Lista';
$background = imageOfTheDay();
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
    <style>
		.header {
			background-image: url(<?= $background; ?>);
		}
	</style>

    <link rel="stylesheet" href="/build/css/app.css">
<?php if(isset($javascriptPersonal)){ 
    echo $javascriptPersonal;
} ?>

<?php if(isset($jsview)){ ?>
    <script type="module" src="/build/js/views/<?= $jsview; ?>.js"></script>
<?php } ?>

</head>
<body>
    <header class="header">
        <div class="contenedor contenido-header">
            <div class="barra">
                <div class="logo">
					<div class="h1 nombre-sitio">Lista de <span><?= $typeList; ?></span></div>
				</div>
                <nav class="navegacion">
                    <a href="/" class="navegacion__enlace">Programas</a>
                    <a href="/musica" class="navegacion__enlace">Música</a>
                </nav>
            </div>
        </div>
    </header>
    