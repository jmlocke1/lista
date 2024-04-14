<?php
namespace App\Routes;

use Controller\SystemController;
use Model\Translate;
use App\util\Traductor;
use App\util\Klog;
use App\util\Util;

class Router{
    public $rutasGET = [];
    public $rutasPOST = [];
    public $rutas = [];

    public function __construct()
    {
        $this->rutas['GET'] = [];
        $this->rutas['POST'] = [];
        $this->rutas['PUT'] = [];
        $this->rutas['DELETE'] = [];
    }

    public function get($url, $fn, $permissions = []){
        $this->rutas['GET'] = $this->recursiveUrl($this->rutas['GET'], urlToArray($url), 0, $fn, $permissions);
    }

    public function post($url, $fn, $permissions = []){
        $this->rutas['POST'] = $this->recursiveUrl($this->rutas['POST'], urlToArray($url), 0, $fn, $permissions);
    }

    public function put($url, $fn, $permissions = []){
        $this->rutas['PUT'] = $this->recursiveUrl($this->rutas['PUT'], urlToArray($url), 0, $fn, $permissions);
    }

    public function delete($url, $fn, $permissions = []){
        $this->rutas['DELETE'] = $this->recursiveUrl($this->rutas['DELETE'], urlToArray($url), 0, $fn, $permissions);
    }

    public function recursiveUrl($arr, $urlArray, $index, $fn = [], $permissions = []){
        // Caso base
        if($index >= count(($urlArray))){
            $arr[':fn'] = $fn;
            $arr[':permissions'] = $permissions;
            return $arr;
        } 
        if(!isset($arr[$urlArray[$index]])) $arr[$urlArray[$index]] = [];
        $arr[$urlArray[$index]] = $this->recursiveUrl($arr[$urlArray[$index]],$urlArray, $index + 1, $fn, $permissions);
        return $arr;
    }

    /**
     * Dado un parámetro de entrada devuelve el nombre de la variable que contiene ese parámetro.
     *
     * @param [type] $var   Parámetro de url
     * @return string       Devuelve un string con el nombre de la variable o una cadena vacía, si no es un parámetro
     */
    public function getVariableFromParam($var): string{
        $inicio = strpos($var, '{');
        $final = strpos($var, '}');
        if(is_numeric($inicio) && is_numeric($final)){
            // Si es una variable opcional, hay que eliminar el signo ? también
            $offset = $this->isOptionalParam($var) ? 2 : 1;
            // No se deberían poner espacios dentro de las llaves, pero contemplaremos esa posibilidad
            // Y quitaremos los espacios en blanco que los rodeen
            $varName = trim(substr($var, $inicio + 1, $final - ($inicio + $offset)));
            // Ahora bien, si ponemos espacios en blanco en el nombre de variable, ya no es admisible
            if($this->thereIsBlankSpace($varName)){
                Klog::error("La variable $var contiene espacios en blanco, los cuales no están permitidos");
                return '';
            }
            return $varName;
        }else{
            return '';
        }
    }

    private function thereIsBlankSpace($varName){
        $blank = strpos($varName, ' ');
        return is_numeric($blank);
    }

    public function isOptionalParam($var){
        $option = strpos($var, '?');
        return is_numeric($option);
    }

    public function thereIsParamHere(array $currentNode){
        $vars['optional']['number'] = 0;
        $vars['fixed']['number'] = 0;
        $vars['optional']['var'] = '';
        $vars['fixed']['var'] = '';
        foreach($currentNode as $key => $node) {
            if($var = $this->getVariableFromParam($key)){
                $type = $this->isOptionalParam($key) ? 'optional' : 'fixed';
                $vars[$type]['number']++;
                $vars[$type]['var'] = $var;
                $vars[$type]['original'] = $key;
                $vars[$type]['node'] = $node;
            }
        }
        return $vars;
    }
    
    /**
     * Función que comprueba una url. Si esa url contiene parámetros fijos u opcionales, los
     * incluye en un array en la variable args.
     * 
     *
     * @param array $currentNode		Rutas definidas en la aplicación. Se corresponde con un nodo de directorio.
     * 							Se inicializa en la primera iteración con el árbol de nodos correspondiente
     * 							al método http empleado, por ejemplo $routes['GET'], esto contendrá un árbol de 
     * 							nodos completo correspondiente al método GET
     * @param array $urlArray	URL a comprobar, pero separando sus nodos en un array indexado
     * @param integer $index	Índice del urlArray a comprobar en la iteración actual
     * @param array $vars		Array donde se van almacenando los parámetros contenidos en la url y sus valores
     * @return array 			Devuelve un array con el nodo final de la url y los posibles parámetros
     */
    public function checkUrl(array $currentNode, array $urlArray, int $index = 0, array $vars = []): array{
        // Caso base
        if($index >= count(($urlArray))) return[
            'finalNode' => $currentNode,
            'args' => $vars
        ];
        if (isset($currentNode[$urlArray[$index]])) {
            return $this->checkUrl($currentNode[$urlArray[$index]], $urlArray, $index + 1, $vars);
        }
        $varLevel = $this->thereIsParamHere($currentNode);
        if($varLevel['fixed']['number'] > 0){
            $vars[$varLevel['fixed']['var']] = $urlArray[$index];
            return $this->checkUrl($currentNode[$varLevel['fixed']['original']], $urlArray, $index + 1, $vars);
        }else if($varLevel['optional']['number'] > 0) {
            // El parámetro es opcional, por tanto tendremos que comprobar dos caminos.
            // Primero copiamos las variables
            $varsOpt = $vars;
            // En el nuevo array de variables copiamos el parámetro opcional, dejando el otro array como estaba
            $varsOpt[$varLevel['optional']['var']] = $urlArray[$index];
            // Primero comprobamos la ruta con el parámetro
            $isOptional = $this->checkUrl($currentNode[$varLevel['optional']['original']], $urlArray, $index + 1, $varsOpt);
            if(!empty($isOptional) && isset($isOptional['finalNode'][':fn'])){
                return $isOptional;
            }else{
                // El parámetro opcional no existe, comprobamos la ruta sin él
                return $this->checkUrl($currentNode[$varLevel['optional']['original']], $urlArray, $index, $vars);
            }
        }else{
            // Definitivamente, no existe la ruta
            return [];
        }
    }
    public function comprobarRutas(){
        $metodo = $_SERVER['REQUEST_METHOD'];
        $urlActual = $_SERVER['REDIRECT_URL'] ?? '/';
        $urlChecked = $this->checkUrl($this->rutas[$metodo], urlToArray($urlActual));
        $fn = $urlChecked['finalNode'][':fn'] ?? [];
        $args = $urlChecked['args'] ?? [];
        // $correctLanguage = $this->setLanguage($args);
        
        $args['router'] = $this;
        
        if(!empty($fn)){
            // La url existe y hay una función asociada
            call_user_func($fn, $args);
        }else if(strpos($urlActual, 'api')){
            header('HTTP/1.1 404 URL Not Found');
        }else{
            header('Location: /404');
            die();
        }
    }


    public function render($view, $datos = [] ) {
        foreach($datos as $key => $value){
            $$key = $value;
        }
        // Este código está comentado por si algún día lo uso
        //ob_start(); // Almacenamiento en memoria durante un momento...
        include DIR_ROOT."/views/$view.php";

        //$contenido = ob_get_clean(); // Limpia el Buffer
        //echo $contenido;
        //include __DIR__."/views/layout.php";
    }
}