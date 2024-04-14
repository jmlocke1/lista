<?php
namespace App\Routes;

use Controller\PruebaController;



$router->get('/indexpru', [PruebaController::class, 'indexpru']);