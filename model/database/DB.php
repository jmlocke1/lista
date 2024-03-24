<?php
namespace Model\database;
use PDO;
use App\util\Klog;
use App\config\Config;

/**
 * Description of DB
 *
 * @author jmizq_z
 */
class DB {
    protected  $conexion;
    protected $msg;
    protected $affectedRows;
    
    // Gestor de conexiones
    protected static $dbManager = [];

    const TABLES = [
        'banknote',
        'banknotedescription',
        'coin',
        'coindescription',
        'country',
        'country_externallink',
        'currency',
        'currency_country',
        'defaultlanguage',
        'externallink',
        'image',
        'imagedescription',
        'iso_639_1',
        'iso_639_2',
        'iso_639_2_b',
        'iso_639_2_t',
        'iso_639_3',
        'labelcategory',
        'languages',
        'languagetype',
        'permission',
        'role',
        'role_permission',
        'scope',
        'statuscodecountry',
        'statustranslate',
        'subdivision',
        'subdivision_externallink',
        'subdivisioncategory',
        'tlabel',
        'translate',
        'translates_into_languages',
        'user',
        'user_permission',
        'user_role',
    ];

    public function __construct(
        $host = Config::DB_HOST,
        $dbName = Config::DB_NAME,
        $dbEncode = Config::DB_ENCODE,
        $dbUsername = Config::DB_USERNAME,
        $dbPassword = Config::DB_PASSWORD
    ){
		$this->conexion = $this->conectar($host, $dbName, $dbEncode, $dbUsername, $dbPassword);
	}
    

    /**
     * Undocumented function
     *
     * @param string $host
     * @param string $dbName
     * @param string $dbEncode
     * @param string $dbUsername
     * @param string $dbPassword
     * @return DB
     */
    public static function getDB(
        $host = Config::DB_HOST,
        $dbName = Config::DB_NAME,
        $dbEncode = Config::DB_ENCODE,
        $dbUsername = Config::DB_USERNAME,
        $dbPassword = Config::DB_PASSWORD
    ): DB {
        if(isset(self::$dbManager[$host][$dbName])){
            return self::$dbManager[$host][$dbName];
        }else{
            self::$dbManager[$host][$dbName] = new static($host, $dbName, $dbEncode, $dbUsername, $dbPassword);
            return self::$dbManager[$host][$dbName];
        }
    }
    
    /**
     * Ejecuta una consulta preparada
     * 
     * @param string $sql       Consulta parametrizada (o no)
     * @param array $valores    Valores para insertar/actualizar. Puede ser un array de arrays de valores
     * @param boolean $multiInsert  Flag que indica si los valores son un array de array de valores
     * @return type
     */
    public function ejecutarConsulta(string $sql, array $valores = null, bool $multiInsert = false) {
        $this->msg = null;
        $exito = true;
        $this->affectedRows = 0;
        try {
            $stmt = $this->conexion->prepare($sql);
            if($multiInsert){
                foreach ($valores as $value) {
                    $exito = $exito && $stmt->execute($value);
                    $this->affectedRows += $stmt->rowCount();
                }
            }else{
                $exito = $stmt->execute($valores);
                $this->affectedRows += $stmt->rowCount();
            }
        } catch (\PDOException $exc) {
            $this->msg = "Error ".$exc->getMessage();
            $exito = false;
        }
        if(!$exito){
            $this->msg .= ". Error en la inserción";
            $errmsg = "Consulta efectuada: ".PHP_EOL;
            $errmsg .= $sql.PHP_EOL;
            $errmsg .= "Valores a insertar: ".json_encode($valores).PHP_EOL;
            $errmsg .= $this->msg;
            Klog::error($errmsg);
        }
        return $stmt;
    }
    
    /**
     * Ejecuta una consulta de inserción de valores y retorna el valor del id
     * del registro recién insertado.
     * 
     * @param string $sql       Consulta de inserción
     * @param array $valores    Valores a insertar
     * @return int              Id del registro recién insertado
     */
     public function ejecutarConsultaRetornarID($sql, $valores){
        $query = $this->ejecutarConsulta($sql, $valores);
        $this->hayError();
        return $this->conexion->lastInsertId();
    }
    
    /**
     * Devuelve el id del último registro insertado
     * 
     * @return int  Id del Último registro insertado
     */
    public function getLastId(){
        return $this->conexion->lastInsertId();
    }
    
    public function ejecutarConsultaSimpleFila($sql, $valores = null){
        $resp = $this->selectAssoc($sql, $valores);
        if(isset($resp) && count($resp) > 0){
            return $resp[0];
        }else{
            return null;
        }
    }
    
    /**
     * Ejecuta una instrucción de inserción o actualización en la base de datos.
     * Se pueden insertar múltiples registros, en cuyo caso hay que poner el parámetro
     * multiInsert en true, y pasar un array indexado de arrays asociativos con
     * los datos de todos los registros.
     * 
     * @param string $sql           Consulta a ejecutar
     * @param array $valores        Valores a insertar
     * @param boolean $multiInsert  Hay varios registros o solo uno
     * @return int                  Número de filas afectadas
     */
    public function insertUpdateQuery($sql, $valores = null, $multiInsert = false): int{
        $stmt = $this->ejecutarConsulta($sql, $valores, $multiInsert);
        return $this->affectedRows;
    }
    
    /**
     * Ejecuta una consulta select y retorna un array asociativo en cada fila
     * 
     * @param string $sql
     * @param array $valores
     * @throws Exception
     */
    public function selectAssoc($sql, $valores = null){
        $resp = $this->ejecutarConsulta($sql, $valores);
        $resultado = [];
        while($fila = $resp->fetch(PDO::FETCH_ASSOC)){
            $resultado[] = $fila;
        }
        $this->hayError();
        return $resultado;
    }
    
    public function selectObject($sql, $valores = null) {
        $resp = $this->ejecutarConsulta($sql, $valores);
        $resultado = [];
        while($fila = $resp->fetch(PDO::FETCH_OBJ)){
            $resultado[] = $fila;
        }
        $this->hayError();
        return $resultado;
    }
    
    /**
     * Comprueba si se ha provocado algún error
     * @throws Exception
     */
    public function hayError() {
        if(isset($this->msg)){
            throw new \Exception($this->msg);
        }
    }
    
    public function getMsg(){
        return $this->msg;
    }
    
    /**
     * Conecta con la base de datos con los datos que hay en Config.php
     */
    protected function conectar($host, $dbName, $dbEncode, $dbUsername, $dbPassword){
        $dsn = "mysql:host=".$host.";dbname=".$dbName.";charset=".$dbEncode;
        try {
            $conexion = new PDO($dsn, $dbUsername, $dbPassword);
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (\PDOException $exc) {
            $this->msg = "Error: ".$exc->getMessage() . " Conectando a la base de datos<br>";
            $this->msg .= $exc->getTraceAsString();
            Klog::error($this->msg);
        }
        return $conexion;
    }
    
    public function getTables(){
        $query = "SHOW TABLES";
        $tables = $this->selectAssoc($query);
        $finalTables = [];
        foreach ($tables as $table) {
            $finalTables[] = $table["Tables_in_worldcode"];
        }
        return $finalTables;
    }
    
    public function compruebaTablas(){
        $tablasConst = $tablasDin = true;
        $tablasDinamicas = $this->getTables();
        foreach (self::TABLES as $constTable) {
            $tablasConst = $tablasConst && in_array($constTable, $tablasDinamicas);
        }
        if($tablasConst){
            echo 'Todas las tablas constantes están en dinámicas';
        }else{
            echo 'No todas las tablas constantes están en dinámicas';
        }
        foreach ($tablasDinamicas as $dinTable) {
            $tablasDin = $tablasDin && in_array($dinTable, self::TABLES);
        }
        if($tablasDin){
            echo 'Todas las tablas dinámicas están en constantes';
        }else{
            echo 'No todas las tablas dinámicas están en constantes';
        }
    }
    
    /**
     * Comienza una transacción
     *
     * @return bool     true si se inicia correctamente, false si no lo hace
     */
    public function beginTransaction(): bool{
        $ok = true;
        try {
            $ok = $this->conexion->beginTransaction();
        } catch (\PDOException $exc) {
            Klog::error($exc->getMessage());
        }
        if(!$ok){
            Klog::error("No se ha podido iniciar la transacción");
        } 
        return $ok;
    }

    /**
     * Guarda todos los cambios efectuados en la transacción
     *
     * @return bool     true si se realiza el commit correctamente.
     */
    public function commit(): bool{
        if(!isset($this->conexion)){
            return false; // Para hacer un commit tiene que haber conexión
        }
        try {
            $commit = $this->conexion->commit();
        } catch (\PDOException $exc) {
            Klog::error($exc->getMessage());
        }
        if(!$commit) Klog::error("No se ha podido realizar el commit");
        return $commit;
    }

    /**
     * Deshace todos los cambios realizados en una transacción
     *
     * @return bool     true si se realiza correctamente el rollback
     */
    public function rollback(){
        if(!isset($this->conexion)){
            return false; // Para hacer un rollback tiene que haber conexión
        }else{
            return $this->conexion->rollBack();
        }
    }

    /**
     * Desactiva la comprobación de claves foráneas
     *
     * @return void
     */
    public function disableForeignKeyChecks(): void{
        $query = "SET foreign_key_checks = 0";
        $this->ejecutarConsulta($query);
    }

    /**
     * Activa la comprobación de claves foráneas
     *
     * @return void
     */
    public function enableForeignKeyChecks(): void{
        $query = "SET foreign_key_checks = 1";
        $this->ejecutarConsulta($query);
    }
}
