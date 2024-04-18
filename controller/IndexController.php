<?php
namespace Controller;

class IndexController {
    public static function index(array $args) {
        $cssPersonal = <<<PRE

    <link href="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.0.4/datatables.min.css" rel="stylesheet">

PRE;
        $javascriptPersonal = <<<PRE

    <script src="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.0.4/datatables.min.js" defer></script>

PRE;
        $router = $args['router'];
        $router->render('index/index', [
            'title' => 'Lista Principal',
            'jsview' => 'index',
            'cssPersonal' => $cssPersonal,
            'javascriptPersonal' => $javascriptPersonal
        ]);
    }
}