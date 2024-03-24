<?php
use Model\ActiveRecord;
use Model\ActiveRecordClasses\AccessData;

require_once "config/app.php";
const FORCE = '--force';


/**
 * Uso de este script:
 * php makemodel.php NombreClase
 * El nombre de la clase hay que ponerlo en CamelCase y sin el .php al final
 */

/**
 * El número de argumentos lo indica $argc.
 * $argv es un array indexado con todos los parámetros, donde el primero
 * es el propio script
 */
if($argc < 2 || in_array('--help', $argv)){
	echo "Debes introducir el nombre del modelo con estilo CamelCase".PHP_EOL;
	echo "Por ejemplo: php makemodel.php LangCode".PHP_EOL;
	echo "Si quieres leer un nombre de tabla concreto: php makemodel.php CountryExternalLink --tablename country_externallink".PHP_EOL;
	die();
}

$nombreClase = $argv[1];
$filename = $nombreClase.".php";
if(file_exists('model/'.$filename) && !in_array(FORCE, $argv)){
	echo "Ya existe el fichero ".$filename.PHP_EOL;
	echo "Si quieres borrarlo de todas maneras, utiliza el parámetro ".FORCE;
	die();
}

if(in_array('--tablename', $argv)){
	$ind = array_search('--tablename', $argv);
	$nombreTabla = $argv[$ind + 1];
}else {
	$nombreTabla = strtolower($nombreClase);
}

print "Nombre de la clase: ". $nombreClase.PHP_EOL;
print "Nombre del fichero: ". $filename.PHP_EOL;
print "Nombre de la tabla: ". $nombreTabla.PHP_EOL;

// Empezando a escribir el fichero
$file = fopen(DIR_ROOT.'/model/'.$filename, "w");
$columnsDB = ActiveRecord::getColumns($nombreTabla);
$columnsOneLine = implode("', '", $columnsDB);
$text = <<<PRE
<?php
namespace Model;

class $nombreClase extends \Model\ActiveRecord {
	const TABLENAME = '$nombreTabla';
	protected static \$columnsDB=['$columnsOneLine'];

PRE;
// Claves primarias, si se tienen que escribir aquí
$primaryKeys = ActiveRecord::getPrimaryKeysFromDatabase($nombreTabla);
if($primaryKeys !== ActiveRecord::$primaryKeys){
	$primaryKeysRow = implode("', '", $primaryKeys);
	$text .= <<< PRE
	public static \$primaryKeys = ['$primaryKeysRow'];

PRE;
}
// Claves autoincrementables, si se tienen que sobreescribir
if(ActiveRecord::areThereAutoIncrementKeys($nombreTabla)){
	$automaticIds = ActiveRecord::getAutoIncrementKeys($nombreTabla);
	// ActiveRecord ya define el campo 'id' como autoincrementable, por lo que
	// si es igual, no hay que sobreescribir nada
	if($automaticIds !== ActiveRecord::$automaticIds){
		$automaticIdsRow = implode("', '", $automaticIds);
		$text .= <<< PRE
	public static \$automaticIds = ['$automaticIdsRow'];

PRE;
	}
}else{
	// Si no hay campos autoincrementables, hay que sobreescribir el array $automaticIds
	$text .= <<< PRE
	public static \$automaticIds = [];

PRE;
}
// Claves foráneas
$foreignKeys = ActiveRecord::getForeignKeys($nombreTabla);
if(!empty($foreignKeys)){
	$text .= <<< PRE
	public static \$foreignKeys = [

PRE;
	$comaFinal = '';
	foreach ($foreignKeys as $foreignKey) {
		$comaInicial = '';
		$text .= <<< PRE
$comaFinal		[

PRE;
		foreach ($foreignKey as $key => $value) {
			$text .= <<< PRE
$comaInicial			"$key" => "$value"
PRE;
			$comaInicial = ','.PHP_EOL;
		}
		$comaFinal = ','.PHP_EOL;
		$text .= <<< PRE
		
		]
PRE;
	}
	$text .= <<< PRE
	
	];

PRE;
}
// Campos de la tabla (OBSOLETO, ya no se definen los campos)
// $text .= PHP_EOL;
// foreach ($columnsDB as $field) {
// 	$text .= <<< PRE
// 	public \$$field;

// PRE;
// }

// getFunctions y setFunctions. Las getFunctions hay que ponerlas
// a mano, según las necesidades. Las setFunctions siempre estarán
// definidas para cada campo, y revisadas a mano por si hay que cambiarlas
$text .= <<< PRE

	protected static \$getFunctions = [];
	protected static \$setFunctions = [

PRE;
$coma = '';
foreach ($columnsDB as $field) {
	$nombreSet = nombreSetFunction($field);
	$text .= <<< PRE
$coma		'$field' => '$nombreSet'
PRE;
	$coma = ','. PHP_EOL;
}

$text .= <<< PRE

	];

PRE;

// Constructor
$text .= <<< PRE

	public function __construct(\$args = []){

PRE;
foreach ($columnsDB as $field) {
	$text .= <<< PRE
		\$this->$field = \$args['$field'] ?? null;

PRE;
}
$text .= <<< PRE
	}

	// Métodos Set

PRE;

$tabla = ActiveRecord::describeTable($nombreTabla);
foreach($tabla as $field){
	$column = $field['Field'];
	$dateTime = false;
	$type = ActiveRecord::getType($field['Type']);
	if($type === 'datetime'){
		$type = 'string';
		$dateTime = true;
	}
	$nombreFuncion = nombreSetFunction($column);

	$text .= <<< PRE
	protected function $nombreFuncion($type \$$column) {

PRE;
	if($dateTime){
		$text .= <<< PRE
		\$this->setDateTime('$column', \$$column);
PRE;
	}
	$limits = ActiveRecord::getLimitsType($field['Type']);
	if($type === 'string' && !$dateTime){
		$text .= <<< PRE
		\$length = strlen(\$$column);
		if(\$length >= {$limits['min']} && \$length <= {$limits['max']}){
			\$this->data['$column'] = \$$column;
		}else{
			\$this->setAlert('error', 'El texto supera la capacidad máxima del campo ({$limits['min']}, {$limits['max']})');
		}
PRE;
	}
	if($type === 'int'){
		$text .= <<< PRE
		if(\$$column >= {$limits['min']} && \$$column <= {$limits['max']}){
			\$this->data['$column'] = \$$column;
		}else{
			\$this->setAlert('error', 'El valor asignado al campo $column está fuera del rango admitido ({$limits['min']}, {$limits['max']})');
		}
PRE;
	}
	if(str_contains($field['Type'], 'decimal')){
		$text .= <<< PRE
		if(\$$column >= {$limits['min']} && \$$column <= {$limits['max']}){
			\$this->data['$column'] = \$$column;
		}else{
			\$this->setAlert('error', 'El valor asignado al campo $column está fuera del rango admitido ({$limits['min']}, {$limits['max']})');
		}
PRE;
	}elseif($type === 'float'){
		$text .= <<< PRE
		\$this->data['$column'] = \$$column;
PRE;
	}
$text .= <<< PRE

	}


PRE;
}

$text .= <<< PRE

	// Métodos particulares del modelo
}
PRE;

// Escribimos el fichero y lo cerramos
fwrite($file, $text);
fclose($file);