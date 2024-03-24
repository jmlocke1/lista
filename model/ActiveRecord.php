<?php
namespace Model;

use Exception;
use Model\ActiveRecordClasses\AccessData;
use Model\ActiveRecordClasses\DBManager;

require_once __DIR__.'/../config/app.php';
/**
 * Clase de la que extienden todos los modelos. 
 * Contiene los métodos comunes a todas las clases de modelo, las clases hijas
 * deben implementar el constructor, definir sus campos y los métodos particulares
 * de cada una.
 * CAMPOS QUE DEBEN SOBREESCRIBIR Y/O DEFINIR OBLIGATORIAMENTE LAS CLASES HIJAS:
 * TABLENAME: Cada modelo tiene un nombre distinto de tabla
 * $columnsdDB: Array con los nombres de los campos de la tabla
 * 
 * CAMPOS QUE DEBEN SOBREESCRIBIR Y/O DEFINIR OPCIONALMENTE LAS CLASES HIJAS:
 * $primaryKeys: Si la o las claves primarias son distintas de 'id', deben definirse en este array
 * $automaticIds: Claves de generación automática. Por defecto es 'id', si son
 *                  distintas, deben definirse aquí. Si no hay claves automáticas,
 *                  se define un array vacío (protected static $automaticIds = [];)
 * CAMPOS CON LAS PROPIEDADES DE LA TABLA
 * Una variable por cada campo de la tabla
 * SANITIZAR LOS CAMPOS
 * Los campos que necesiten algún tipo de sanitización se definirán dentro del método
 * sanitizeArguments() en la clase hija. Al definirse en un método, podrá ser usado en
 * el constructor y en el método syncronize
 */
abstract class ActiveRecord extends DBManager {
    
    

    /**
     * Objetos para guardar de manera masiva, o sea, varios objetos
     * a la vez.
     * @deprecated 0.2
     * @var array
     */
    public static array $objectsToSave = [];
    /**
     * Objetos para crear de manera masiva, o sea, varios objetos
     * a la vez.
     * 
     * @var array
     */
    public static array $objectsToCreate = [];
    /**
     * Objetos para actualizar de manera masiva, o sea, varios objetos
     * a la vez.
     * 
     * @var array
     */
    public static array $objectsToUpdate = [];

    public static function factory(array $args = []): static {
        return new static($args);
    }
    
    
    /**
     * Quita los ids autoincrementables del array
     * 
     * @param array $array
     * @return array
     */
    protected static function quitaIds(array $array){
        foreach(static::$automaticIds as $id){
            if(isset($array[$id])){
                unset($array[$id]);
            }
        }
        return $array;
    }


    public function validate() {
        
    }

    

    

    /**
     * Guarda los datos del objeto en la base de datos
     * ¡¡ATENCIÓN!! Función muy insegura, no se puede garantizar
     * que una operación sea de actualización o creación. Hay que usar las funciones
     * correspondientes create y update
     * @deprecated version
     * @return type
     */
    public function save() {
        $resultado = '';
        if(property_exists($this, $this->id) && !is_null($this->id)) {
            // actualizar
            $resultado = $this->update();
        } else {
            // Creando un nuevo registro
            $resultado = $this->create();
        }
        return $resultado;
    }
    

    /**
     * crea un nuevo registro
     *
     * @return integer
     */
    public function create(): int {
        // Sanitizar los datos (obsoleto, ahora hay que sanitizarlos en la entrada)
        $atributos = $this->getAttributes(['includeNullValues' => false]);
        $values = self::convertDataForQuery($atributos);
        // Insertar en la base de datos
        $query = $this->queryForCreate($atributos);
        // Resultado de la consulta
        $resultado = self::$db->insertUpdateQuery($query, $values);
        
        return $resultado;
    }

    public static function createAll(): int {
        $objects = self::getObjectsToCreate();
        if(count($objects) === 0){
            return 0;
        }
        // Inicializamos los valores a insertar
        $values = [];
        $atributos = [];
        foreach ($objects as $key => $object) {
            $atributos = $object->getAttributes(['includeNullValues' => false]);
            $values[] = self::convertDataForQuery($atributos);
        }
        $query = self::queryForCreate($atributos);
        $resultado = self::$db->insertUpdateQuery($query, $values, true);
        if(!$resultado){
            self::setAlert('error', self::$db->getMsg());
        }
        return $resultado;
    }

    /**
     * Devuelve todos los objetos guardados anteriormente para crear varios registros.
     * Los objetos devueltos son de la misma clase que la clase llamante. Por ejemplo:
     * $objects = Tlabel::getObjectsToSave();
     * Devolverá exclusivamente los objetos de la clase Tlabel, aunque se hayan guardado objetos de 
     * otras clases.
     * Si no se pasa ningún parámetro, se borrarán los registros guardados de esa clase.
     * 
     * @param boolean $delete   
     * @return array
     */
    public static function getObjectsToCreate(bool $delete = true): array {
        $objects = static::$objectsToCreate[static::class] ?? [];
        if($delete) unset (static::$objectsToCreate[static::class]);
        return  $objects;
    }
    
    protected static function queryForCreate($atributos){
        $query = "INSERT INTO " . static::TABLENAME . " (";
        $query .= join(', ', array_keys($atributos));
        $query .= ") VALUES (:"; 
        $query .= join(", :", array_keys($atributos));
        $query .= ")";
        return $query;
    }

    protected static function messageQuery($resultado){
        if($resultado){
            $respuesta = [
                'resultado' =>  $resultado
            ];
            if(!empty(static::$automaticIds)){
                $respuesta[static::$automaticIds[0]] = self::$db->getLastId();
            }
        }else{
            $respuesta = [
                'resultado' => $resultado,
                "error" => self::$db->getMsg()

            ];
        }
        return $respuesta;
    }

    public static function getFieldsToUpdate($attributes){
        $fieldsToUpdate = [];
        foreach($attributes as $key => $value) {
            $fieldsToUpdate[] = "{$key}=:{$key}";
        }
        return $fieldsToUpdate;
    }

    /**
     * Actualizar el registro
     *
     * @param bool  $includeUpdated     Indica si se debe guardar el campo updated o no
     * @return int  El número de filas afectadas
     */
    public function update($includeUpdated = false): int {
        $valuesForQuery = self::getValuesForUpdate($this, $includeUpdated);
        // Consulta SQL
        $query = self::queryForUpdate($this, $includeUpdated);
        // Actualizar BD
        $resultado = self::$db->insertUpdateQuery($query, $valuesForQuery);
        return $resultado;
    }

    /**
     * Actualiza todos los objetos añadidos anteriormente. Solo actualiza los
     * objetos de la misma clase que la clase llamante
     *
     * @param bool  $includeUpdated     Indica si se debe guardar el campo updated o no
     * @return int  El número de filas afectadas
     */
    public static function updateAll(bool $includeUpdated = false): int {
        $objects = self::getObjectsToUpdate();
        if(count($objects) === 0){
            return 0;
        }
        $values = [];
        foreach ($objects as $object) {
            // Agregamos a los valores para actualizar
            $values[] = self::getValuesForUpdate($object, $includeUpdated);
        }
        // Consulta SQL
        $query = self::queryForUpdate($objects[0], $includeUpdated);
        // Actualizar BD
        $resultado = self::$db->insertUpdateQuery($query, $values, true);
        return $resultado;
    }

    /**
     * Devuelve todos los objetos guardados anteriormente para crear o actualizar varios registros.
     * Los objetos devueltos son de la misma clase que la clase llamante. Por ejemplo:
     * $objects = Tlabel::getObjectsToSave();
     * Devolverá exclusivamente los objetos de la clase Tlabel, aunque se hayan guardado objetos de 
     * otras clases.
     * Si no se pasa ningún parámetro, se borrarán los registros guardados de esa clase.
     * 
     * @param boolean $delete   
     * @return array
     */
    public static function getObjectsToUpdate(bool $delete = true): array {
        $objects = static::$objectsToUpdate[static::class] ?? [];
        if($delete) unset (static::$objectsToUpdate[static::class]);
        return  $objects;
    }

    public static function validateAll(){
        $validate = true;
        $objects = static::getObjectsToCreate(false);
        foreach($objects as $object){
            $validate = $validate && $object->validate();
        }
        $objects = static::getObjectsToUpdate(false);
        foreach($objects as $object){
            $validate = $validate && $object->validate();
        }
        return $validate;
    }
    
	protected static function getValuesForUpdate(ActiveRecord $object, bool $includeUpdated = false): array {
		$attributes = self::getAttributesForUpdate($object, $includeUpdated);
		$primaryKeys = $object->getPrimaryKeysValues();
        $valuesForQuery = array_merge($attributes, $primaryKeys);
        $valuesForQuery = self::convertDataForQuery($valuesForQuery);
		return $valuesForQuery;
	}
    
    /**
     * Genera una consulta de actualización
     *
     * @param [type] $fieldsToUpdate
     * @param [type] $primaryInWhere
     * @return void
     */
    protected static function queryForUpdate(ActiveRecord $object, bool $includeUpdated = false){
		$attributes = self::getAttributesForUpdate($object, $includeUpdated);
		$fieldsToUpdate = self::getFieldsToUpdate($attributes);
		$primaryKeys = $object->getPrimaryKeysValues();
		$primaryInWhere = self::makeVariablesQuery($primaryKeys);
        // Consulta SQL
        $query = "UPDATE " . static::TABLENAME ." SET ";
        $query .=  join(', ', $fieldsToUpdate );
        $query .= " WHERE " . $primaryInWhere;
        $query .= " LIMIT 1";
        return $query;
    }


    /**
     * Undocumented function
     *
     * @param [type] $obj
     * @return boolean
     */
    public static function addToCreate(object $obj): bool {
        if($obj::class === static::class){
            static::$objectsToCreate[$obj::class][] = $obj;
            return true;
        }else{
            self::setAlert("error", "El objeto pasado no es de la misma clase que la clase hija");
            return false;
        }
    }

    /**
     * Undocumented function
     *
     * @param [type] $obj
     * @return boolean
     */
    public static function addToUpdate(object $obj): bool {
        if($obj::class === static::class){
            static::$objectsToUpdate[$obj::class][] = $obj;
            return true;
        }else{
            self::setAlert("error", "El objeto pasado no es de la misma clase que la clase hija");
            return false;
        }
    }

    public static function saveAll($delete = true):bool {
        $created = self::createAll($delete);
        if(!$created){
            self::setAlert('error', 'No se han podido crear todos los objetos');
        }
        $updated = self::updateAll($delete);
        if(!$updated){
            self::setAlert('error', 'No se han podido actualizar todos los objetos');
        }
        return $created && $updated;
    }
     /**
      * Eliminar un Registro por su o sus clave/s primaria/s
      *
      * @return void
      */
    public function delete() {
        $primaryKeys = $this->getPrimaryKeysValues();
        $primaryInWhere = self::makeVariablesQuery($primaryKeys);
        $valuesForQuery = self::convertDataForQuery($primaryKeys);
        $query = "DELETE FROM "  . static::TABLENAME . " WHERE " . $primaryInWhere . " LIMIT 1";
        
        $resultado = self::$db->insertUpdateQuery($query, $valuesForQuery);

        return $resultado;
    }

    /**
     * Función donde se sanitizan los argumentos. En ActiveRecord esta función
     * no hace nada, pero se define aquí por conveniencia, ya que el método
     * syncronize hace uso de ella. Si se definiera como abstracta sería
     * obligatorio definirla en todas las clases hijas, se utilice o no, por
     * lo que este planteamiento es mucho más cómodo.
     *
     * @return void
     */
    protected function sanitizeArguments(){
        
    }

    public static function getLastCreated(){
        if(!in_array('created', static::$columnsDB)){
            return '';
        }
        $query = "SELECT MAX(`created`) AS created FROM ".static::TABLENAME;
        $resultado = self::$db->ejecutarConsultaSimpleFila($query);
        return $resultado['created'];
    }

    public static function getLastUpdated(){
        if(!in_array('updated', static::$columnsDB)){
            return '';
        }
        $query = "SELECT MAX(`updated`) AS updated FROM ".static::TABLENAME;
        $resultado = self::$db->ejecutarConsultaSimpleFila($query);
        return $resultado['updated'];
    }
}