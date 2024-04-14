<?php
namespace Controller;


class PruebaController {
	public static function indexpru(array $args){
        $router = $args['router'];
		$router->render('prueba/indexpru');
	}

}