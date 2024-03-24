<?php
declare(strict_types=1);
namespace Model\ActiveRecordClasses;

use Model\Traits\ErrorManager;
use Model\Traits\UtilActiveRecord;
/**
 * Undocumented class
 */
class AccessData {
	use ErrorManager, UtilActiveRecord;
	/**
	 * Valores de los campos. Se definen al construir el objeto de un modelo.
	 * Ejemplo de valores:
	 * 		$data = [
	 * 			'name' => 'José Miguel',
	 * 			'surname' => 'Izquierdo Martínez'
	 * 		];
	 *
	 * @var array 	Array asociativo, donde la clave es el nombre de campo y el valor es su valor
	 */
	protected $data = [];

	/**
	 * Columnas de la base de datos. Se debe asignar su valor en el modelo mediante un array indexado.
	 *
	 * @var array 	Array indexado con los nombres de los campos
	 */
	protected static $columnsDB = [];
	

	/**
	 * Funciones get. Si hay funciones get que manipulen los datos, se debe definir el nombre de la función en este array 
	 * (en el modelo que haga uso de ella).
	 * Por ejemplo, una fecha puede estar almacenada como un string, pero si se devuelve el valor, nos puede interesar 
	 * que devuelva un objeto Date. Si no hay que realizar ninguna manipulación en el dato, debemos dejar
	 * que PHP realice su magia con __get()
	 * Ejemplo:
	 * 		$getFunctions = [
	 * 			'name' => 'getName',
	 * 			'surname' => 'getSurname'
	 * 		];
	 *
	 * @var array 	Array asociativo, siendo la clave el nombre de campo y el valor el nombre de la función
 	 */
	protected static $getFunctions = [];

	protected static $commonGetFunctions = [
		'data' => 'getData',
		'columnsDB' => 'getColumnsDB'
	];
	/**
	 * Funciones set. Si se quiere controlar qué dato se está suministrando al campo, se debe definir la función en este array 
	 * (en el modelo que haga uso de ella).
	 * El uso típico de estas funciones es controlar exhaustivamente el tipo de dato a insertar en el campo
	 * 		$setFunctions = [
	 *			'name' => 'setName',
	 *			'surname' => 'setSurname',
	 *			'pru' => 'setPru'
	 * 		];
	 *
	 * @var array
	 */
	protected static $setFunctions = [];

	public function __set($name, $value)
    {
		$esCampo = in_array($name, static::$columnsDB);
		if($esCampo && is_null($value)){
			// Si tiene un valor nulo, se asigna directamente
			$this->data["{$name}"] = $value;
		}elseif(array_key_exists($name, static::$setFunctions)){
			$fun = static::$setFunctions[$name];
			try {
				$this->$fun($value);
			} catch (\Throwable $th) {
				self::setAlert('error', "Error al asignar el valor a {$name}: ". $th->getMessage());
			}
			
		}elseif($esCampo){
			$this->data["{$name}"] = $value;
		}else{
			$trace = debug_backtrace();
			trigger_error(
				'Propiedad indefinida mediante __set(): ' . $name .
				' en ' . $trace[0]['file'] .
				' en la línea ' . $trace[0]['line'],
				E_USER_NOTICE);
		}
        
    }

	public function __get($name)
    {
		if(array_key_exists($name, static::$commonGetFunctions)){
			$fun = static::$commonGetFunctions[$name];
			return $this->$fun();
		}
		if(array_key_exists($name, static::$getFunctions)){
			$fun = static::$getFunctions[$name];
			return $this->$fun();
		}
        if (in_array($name, static::$columnsDB)) {
			return $this->data["{$name}"] ?? null;
        }
		
        $trace = debug_backtrace();
        trigger_error(
            'Propiedad indefinida mediante __get(): ' . $name .
            ' en ' . $trace[0]['file'] .
            ' en la línea ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }

	public function __isset($name) {
		return isset($this->data["{$name}"]);
	}

	public function __unset($name)
    {

        unset($this->data["{$name}"]);
    }

	public function __serialize(): array
	{
		return $this->data;
	}
	public function __unserialize(array $data): void
	{
		$this->data = $data;
	}

	/**
	 * Codifica los datos a json para mandarlos al cliente
	 *
	 * @return string
	 */
	public function json(): string {
		return json_encode($this->data);
	}

	/**
	 * Devuelve el array de datos de los campos de la tabla
	 *
	 * @return array
	 */
	public function getData(): array{
		return $this->data;
	}

	/**
	 * 
	 */
	public function setDateTime(string $name, string | null $date): void {
		try {
			$dateTime = new \DateTime($date);
			$this->data[$name] = $dateTime->format('Y-m-d H:i:s');
		} catch (\Throwable $th) {
			$this->setAlert('error', 'Formato inválido de fecha');
		}
	}
}