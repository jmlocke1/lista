<?php
namespace Model\ActiveRecordClasses;

use App\config\Config;
use Model\database\DB;
/**
 * DBManager es la clase base de la que hereda ActiveRecord y todas
 * sus subclases, pero que interactúa más directamente con la base de datos
 */
class DBManager extends AccessData {
    
	/**
     * Clase estática que maneja la base de datos
     * 
     * @var  Nombre de la clase estática que maneja la base de datos
     */
    protected static $db;

    /**
     * Clave o claves primarias. Si la clave primaria es diferente de 'id', hay que
     * sobreescribir este array en la clase hija. Si hay más de una clave primaria, 
     * se añaden cuantos valores hagan falta
     */
    public static $primaryKeys = ['id'];

    
    
    /**
     * Claves foráneas de la aplicación. la clave asociativa es la clave foránea
     * y el valor es la tabla:
     * $foreignKeys[foreignkey] = tablename
     */
    public static $foreignKeys = [];

    /**
     * Claves primarias que son incrementadas automáticamente. Estas claves no 
     * se introducen cuando se crea un nuevo registro. El valor por defecto es
     * la clave 'id', si se establece otro u otros nombres como claves primarias
     * automáticas hay que sobreescribir este array en la clase hija
     */
    public static $automaticIds = ['id'];

    public const TABLENAME = '';

    /**
     * Variable para montar consultas
     * @var string
     */
    protected static string $where = '';
    protected static array $values = [];

	/**
     * Consulta SQL para crear un objeto en Memoria
     *
     * @param string $query         Consulta SQL
     * @param array|null $values    Posibles valores a asignar a la consulta
     * @return array
     */
    public static function consultarSQL(string $query, array | null $values = null): array {
        // Consultar la base de datos
        $resultado = self::$db->selectAssoc($query, $values);
        // Iterar los resultados
        $array = [];
        foreach($resultado as $registro){
            $array[] = self::createObject($registro);
        }
        // retornar los resultados
        return $array;
    }

    // Crea el objeto en memoria que es igual al de la BD
    protected static function createObject($register) {
        $alertas = self::getAlerts();
        $numPreviousAlerts = count($alertas);
        $data = [];
        foreach($register as $key => $value ) {
            // No es necesario comprobar si existe la propiedad, si no existiera se crearía una alerta
            $data["{$key}"] = $value;
        }
        $object = new static($data);
        $alertas = self::getAlerts();
        if(count($alertas) > $numPreviousAlerts){
            return new static;
        }else{
            return $object;
        }
    }

	/**
     * Devuelve un array asociativo con el valor de las claves primarias
     * @return array
     */
    public function getPrimaryKeysValues(){
        $keys = [];
        foreach (static::$primaryKeys as $pkName) {
            $keys[$pkName] = $this->$pkName;
        }
        return $keys;
    }

    /**
     * Devuelve un array con las claves primarias
     *
     * @return array
     */
    public static function getPrimaryKeys(): array {
        return static::$primaryKeys;
    }

    /**
     * Crea un array válido de clave o claves primarias a partir del array de entrada.
     * El array de entrada puede ser asociativo o indexado. Si es indexado, el orden de los
     * valores debe coincidir con el orden en el que están las claves primarias, aunque hay que avisar
     * que esta forma puede ser propensa a errores.
     * Si es asociativo, no importa el orden en el que se introduzcan los valores, pues cada uno
     * estará asociado a su clave. Es altamente recomendable usar esta forma para evitar errores
     *
     * @param array $arr    Array con las claves primarias del objeto
     * @return array        Devuelve un array asociativo válido con las claves primarias 
     */
    public static function makeValidPrimaryKeyArray(array $arr): array | null{
        if(empty($arr)) {
            self::setAlert('error', 'El array pasado como primary key está vacío');
            return null;
        }
        $sameElements = count($arr) === count(static::$primaryKeys);
        if(!$sameElements){
            self::setAlert('error', 'El array pasado como primary key no es válido');
            return null;
        }
        if(!isAssociativeArray($arr)){
            $arr = array_combine(static::$primaryKeys, $arr);
        }else{
            // Comprobamos si las claves son iguales
            $keys = array_keys($arr);
            $igual = true;
            foreach (static::$primaryKeys as $primary) {
                $igual = $igual && in_array($primary, $keys);
            }
            if(!$igual){
                self::setAlert('error', 'Las claves primarias no son correctas');
                return null;
            }
        }
        return $arr;
    }

    /**
     * Función que recibe un array de datos para insertar y
     * les añade a las claves el carácter dos puntos para poder construir 
     * la consulta
     *
     * @param array $fields
     * @return string
     */
    protected static function makeVariablesQuery(array $fields){
        $query = '';
        $and = '';
        foreach ($fields as $key => $value) {
            $query .= "$and $key=:$key";
            $and = ' AND';
        }
        return $query;
    }

    /**
     * Función que convierte un array asociativo con los valores del objeto en un array asociativo
     * válido para realizar una consulta preparada. En este caso, le añade a la clave el carácter ':'
     * para que coincida con las variables de la consulta.
     *
     * @param array $values Array asociativo con los valores
     * @return array        Devuelve un array asociativo válido para realizar una consulta preparada
     */
    protected static function convertDataForQuery(array $values): array {
        $valueResult = [];
        foreach ($values as $key => $value) {
            $valueResult[":{$key}"] = $value;
        }
        return $valueResult;
    }

    /**
     * Busca un registro por su id. Si la clave primaria es múltiple,
     * se puede llamar a la función de dos maneras, con un array asociativo
     * y con un array indexado.
     * Ejemplo de array asociativo:
     * find(['name' => 'LC_COUNTRY'])
     * Ejemplo de array indexado:
     * find(['LC_COUNTRY'])
     * Los dos devuelven el mismo contenido, un objeto, o null si no es
     * encontrado. Si devuelve null, pueden haber errores documentados
     * que se pueden recuperar con getAlerts.
     *
     * @param array|int $id
     * @return static | null
     */
    public static function find(array|int|string $id): static | null{
        if(is_array($id)){
            $arr = self::makeValidPrimaryKeyArray($id);
        }else{
            $arr = self::makeValidPrimaryKeyArray([$id]);
        }
        if(is_null($arr)) return null;
        return self::findElement($arr);
    }

    
    /**
     * Función auxiliar para encontrar un elemento a partir de su o sus primary
     * keys
     * @param array $arr
     * @return static | null
     */
    protected static function findElement(array $arr): static | null{
        $query = "SELECT * FROM " . static::TABLENAME  ." WHERE";
        $query .= self::makeVariablesQuery($arr);
        $valores = self::convertDataForQuery($arr);
        $query .= " LIMIT 1";
        $resultado = self::consultarSQL($query, $valores);
        return array_shift( $resultado );
    }

    /**
     * Devuelve un array indexado con las columnas de la tabla pasada por parámetro.
     * Si no se pasa ninguna tabla, se obtendrán las columnas de la tabla de la clase
     * llamante. Por ejemplo, si se escribe:
     * Translate::getColumns();
     * obtendremos las columnas de la tabla translate
     *
     * @param [string] $tablename
     * @return array
     */
    public static function getColumns(string $tablename = null): array{
        $tablename = $tablename ?? static::TABLENAME;
        $query = "DESCRIBE ". $tablename;
        $resultado = self::$db->selectAssoc($query);
        $columns = [];
        foreach($resultado as $row){
            $columns[] = $row['Field'];
        }
        return $columns;
    }

    public static function describeTable(string $tablename = null) {
        $tablename = $tablename ?? static::TABLENAME;
        $query = "DESCRIBE ". $tablename;
        return self::$db->selectAssoc($query);
    }

    /**
     * Devuelve la o las claves primarias que hay en una tabla
     *
     * @param [string] $tablename
     * @return array
     */
    public static function getPrimaryKeysFromDatabase(string $tablename = null): array{
        $tablename = $tablename ?? static::TABLENAME;
        $query = "SHOW KEYS from ".$tablename." WHERE Key_name='PRIMARY'";
        $resultado = self::$db->selectAssoc($query);
        $primary = [];
        foreach ($resultado as $row) {
            $primary[] = $row['Column_name'];
        }
        return $primary;
    }

    /**
     * Devuelve un array con el nombre de las claves autoincrementables
     *
     * @param string|null $tablename
     * @return array    Array indexado con los nombres de las claves autoincrementables
     */
    public static function getAutoIncrementKeys(string $tablename = null): array{
        $tablename = $tablename ?? static::TABLENAME;
        $query = "SHOW columns FROM ".$tablename." WHERE Extra='auto_increment'";
        $resultado = self::$db->selectAssoc($query);
        $autoIncrement = [];
        foreach($resultado as $auto){
            $autoIncrement[] = $auto['Field'];
        }
        return $autoIncrement;
    }

    /**
     * Devuelve todos los registros de una tabla
     * @return array    Array de objetos con todos los registros de una tabla
     */
    public static function all(): array {
        $query = "SELECT * FROM " . static::TABLENAME;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    /**
     * Obtener Registros con cierta cantidad
     * 
     * @param type $limite  Límite de registros a entregar
     * @param type $offset  Registro de comienzo
     * @return array
     */
    public static function get($limite = null, $offset = null): array {
        if(empty(self::$where)){
            $query = "SELECT * FROM " . static::TABLENAME;
        }else{
            $query = self::$where;
        }
        if(!is_null($limite)){
            $query .= " LIMIT {$limite}";
        }
        if(!is_null($offset)){
            $query .= " OFFSET {$offset}";
        }
        $resultado = self::consultarSQL($query, self::$values);
        self::$where = '';
        self::$values = [];
        return $resultado;
    }
    
     /**
     * Devuelve el primer elemento de la función where. Si no se ha utilizado anteriormente where, devuelve el
     * primer elemento de la tabla
     *
     * @return static|null
     */
    public static function first(): static|null{
        // Como solo queremos el primer elemento, limitamos la búsqueda a uno
        $resultado = self::get(1);
        return array_shift($resultado);
    }

    private static function checkColumn(string $column): bool {
        $validColumn = in_array($column, static::$columnsDB);
        if(!$validColumn){
            self::setAlert('error', 'El campo introducido no es válido');
        }
        return $validColumn;
    }
    
    /**
     * Busca registros por el valor de una columna. Se pueden montar consultas
     * complejas anidando wheres.
     * Los atributos obligatorios son columna y value. Si solo se suministran dos
     * atributos, se considera que la operación es '=', y el valor es el segundo
     * atributo. Si hay tres atributos, el segundo atributo debe ser la operación
     * 
     * Para obtener los resultados, la última orden es get
     * 
     * @param string $column
     * @param string $valor
     * @param string $operation   Operación a realizar, si no se indica, la operación es =
     * @return type
     */
    public static function where(string $column, string $operation, $value = '') {
        if(!self::checkColumn($column)) return static::class;
        $numArgs = func_num_args();
        if($numArgs === 2){
            $value = $operation;
            $operation = '=';
        }
        $numValue = count(self::$values);
        if(empty(self::$where)){
            self::$where = "SELECT * FROM " . static::TABLENAME . " WHERE {$column} {$operation} :{$column}".$numValue;
        }else{
            return self::and($column, $operation, $value);
        }
        
        self::$values[":$column".$numValue] = $value;
        return static::class;
    }

    public static function and($column, $operation, $value = ''){
        if(!self::checkColumn($column)) return static::class;
        $numArgs = func_num_args();
        if($numArgs === 2){
            $value = $operation;
            $operation = '=';
        }
        if(empty(self::$where)){
            return self::where($column, $operation, $value);
        }else{
            $numValue = count(self::$values);
            self::$where .= " AND {$column} {$operation} :{$column}".$numValue;
        }
        self::$values[":$column".$numValue] = $value;
        return static::class;
    }

    public static function or($column, $operation, $value = ''){
        if(!self::checkColumn($column)) return static::class;
        $numArgs = func_num_args();
        if($numArgs === 2){
            $value = $operation;
            $operation = '=';
        }
        if(empty(self::$where)){
            return self::where($column, $operation, $value);
        }else{
            $numValue = count(self::$values);
            self::$where .= " OR {$column} $operation :{$column}".$numValue;
        }
        self::$values[":$column".$numValue] = $value;
        return static::class;
    }

    public static function order($column, $typeOrder = 'ASC'){
        if(empty(self::$where)){
            self::$where = "SELECT * FROM " . static::TABLENAME;
        }
        $typeOrder = in_array($typeOrder, ['ASC', 'DESC']) ? $typeOrder : 'ASC';
        self::$where .= " ORDER BY {$column} {$typeOrder}";
        return static::class;
    }

    /**
     * Consulta plana de SQL. Utilizar cuando los métodos del modelo 
     * no son suficientes
     *
     * @param [type] $columna
     * @param [type] $valor
     * @return void
     */
    public static function sql($query) {
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    /**
     * Función que indica si una determinada tabla o modelo tiene claves
     * autoincrementables.
     *
     * @param string|null $tablename
     * @return boolean      true, sí hay claves autoincrementables, false si no las hay
     */
    public static function areThereAutoIncrementKeys(string $tablename = null):bool {
        $tablename = $tablename ?? static::TABLENAME;
        $query = "SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES ";
        $query .= "WHERE TABLE_SCHEMA = '".Config::DB_NAME."' AND TABLE_NAME='".$tablename."'";
        $resultado = self::$db->selectAssoc($query);
        $hasAutoIncrement = $resultado[0]['AUTO_INCREMENT'] > 0;
        return $hasAutoIncrement;
    }

    /**
     * Devuelve las claves foráneas de una tabla dada, o la tabla del modelo desde el que se llama,
     * si no se indica ninguna.
     * Los campos son:
     * COLUMN_NAME:             Columna de la tabla que contiene la clave foránea
     * REFERENCED_TABLE_NAME:   Tabla referenciada
     * REFERENCED_COLUMN_NAME:  Adivina... ¡Exacto! Columna referenciada
     *
     * @param string|null $tablename
     * @return array
     */
    public static function getForeignKeys(string $tablename = null): array{
        $tablename = $tablename ?? static::TABLENAME;
        $query = "select COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME from INFORMATION_SCHEMA.KEY_COLUMN_USAGE ";
        $query .= "WHERE REFERENCED_TABLE_SCHEMA='".Config::DB_NAME."' AND TABLE_NAME='".$tablename."'";
        $resultado = self::$db->selectAssoc($query);
        return $resultado;
    }
    
    // Definir la conexión a la BD - includes/database.php
    public static function setDB($database) {
        self::$db = $database;
    }

    public static function getDB(){
        return self::$db ?? null;
    }

    /**
     * Devuelve los atributos del modelo. Si se quieren incluir los ids
     * autoincrementables, hay que pasar true como parámetro. De esta manera,
     * devolverá todos los atributos del modelo.
     * Opciones:
     * includeAutomaticIds: bool Incluye los ids autoincrementables. False por defecto<br>
     * includeNullValues:   bool - Si es true, se incluyen también los valores 
     *                      nulos. True por defecto
     * includePrimaryKeys:  bool - Si es true, se incluyen las primary keys. True por defecto
     * includeUpdated:      bool - Si es true (valor por defecto) se incluye el campo updated
     * 
     * @param array $args Opciones de la función
     * @return array
     */
    public function getAttributes(array $args = []): array {
        $includeAutomaticIds = $args['includeAutomaticIds'] ?? true;
        $includeNullValues = $args['includeNullValues'] ?? true;
        $includePrimaryKeys = $args['includePrimaryKeys'] ?? true;
        $includeUpdated = $args['includeUpdated'] ?? true;
        $attributes = [];
        foreach(static::$columnsDB as $column) {
            if(!$includeAutomaticIds && in_array($column, static::$automaticIds)) continue;
            if(!$includeNullValues && is_null($this->$column)) continue;
            if(!$includePrimaryKeys && in_array($column, static::$primaryKeys)) continue;
            if(!$includeUpdated && $column === 'updated') continue;
            $attributes[$column] = $this->$column;
        }
        return $attributes;
    }

    public static function getAttributesForUpdate($object, $includeUpdated = false){
        $attributes = $object->getAttributes([
            'includePrimaryKeys' => false,
            'includeAutomaticIds' => false,
            'includeNullValues' => true,
            'includeUpdated' => $includeUpdated // Se debe actualizar la fecha en la base de datos, salvo que interese actualizar la presente
        ]);
        return $attributes;
    }

    // Sincroniza BD con Objetos en memoria
    public function syncronize($args=[]) { 
        foreach($args as $key => $value) {
            if(in_array($key, static::$columnsDB)) {
                $this->$key = $value;
            }
        }
    }

    public static function getLastId(){
        if(!empty(static::$automaticIds)){
            return self::$db->getLastId();
        }
    }
}

DBManager::setDB(DB::getDB());