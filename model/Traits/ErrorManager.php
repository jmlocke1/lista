<?php
namespace Model\Traits;

use Exception;

trait ErrorManager {

	// Alertas y Mensajes
    protected static array $alerts = [];

	/**
     * Establece una alerta de un determinado tipo. El tipo solo puede ser error o success
     *
     * @param string $tipo      error o success, para indicar error o éxito
     * @param string $mensaje
     * @return void
     */
    public static function setAlert(string $tipo, string $mensaje): void {
        if($tipo === "error" || $tipo === "success"){
            static::$alerts[$tipo][] = $mensaje;
        }else{
            throw new Exception("El tipo indicado para la alerta, {$tipo}, no es un tipo admitido");
        }
    }

    /**
     * Añade un array de alertas al array de alertas del objeto ActiveRecord
     */
    public static function addAlerts(array $alertas){
        foreach($alertas as $tipo => $alerta){
            foreach ($alerta as $mensaje) {
                self::setAlert($tipo, $mensaje);
            }
        }
    }

    /**
	 * Devuelve un array de alertas de error o éxito
	 *
	 * @param boolean 	$remove		true: Elimina el array, false o no poner nada: Devuelve el array de alertas, pero no lo elimina
	 * @return array 	Array de alertas de error o éxito
	 */
    public static function getAlerts($remove = false): array {
        $alerts = static::$alerts;
        if($remove) self::removeAlerts();
        return $alerts;
    }

    /**
     * Elimina todas las alertas que pudieran estar almacenadas
     */
    public static function removeAlerts(){
        static::$alerts = [];
    }
}