<?php
require_once __DIR__.'/../config/app.php';

use App\Routes\Router;
use Controller\IndexController;

$router = new Router();

// Rutas de prueba
include DIR_ROOT . '/Routes/routesPrueba.php';

$router->get('/', [IndexController::class, 'index']);
$router->get('/musica', [IndexController::class, 'musica']);
$router->get('/comics', [IndexController::class, 'comics']);
$router->get('/peliculas', [IndexController::class, 'peliculas']);


$router->comprobarRutas();