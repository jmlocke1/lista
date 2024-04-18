<?php
namespace Controller;

use App\config\Plugins;

class IndexController {
    public static function index(array $args) {
        $router = $args['router'];
        $router->render('index/index', [
            'title' => 'Lista Principal',
            'typeList' => 'Programas',
            'jsview' => 'index',
            'cssPersonal' => Plugins::DATATABLES_CSS,
            'javascriptPersonal' => Plugins::DATATABLES_JS
        ]);
    }

    public static function musica(array $args) {
        $router = $args['router'];
        $router->render('index/musica', [
            'title' => 'Lista Principal',
            'typeList' => 'Música',
            'jsview' => 'index',
            'cssPersonal' => Plugins::DATATABLES_CSS,
            'javascriptPersonal' => Plugins::DATATABLES_JS
        ]);
    }
}