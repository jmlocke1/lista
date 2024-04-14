<?php
namespace Controller;

class IndexController {
    public static function index(array $args) {
        $router = $args['router'];
        $router->render('index/index');
    }
}