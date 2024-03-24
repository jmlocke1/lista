<?php
namespace Model\Traits;

trait UtilActiveRecord {
	
	

    public static function getType(string $type): string {
        if(
            str_contains($type, 'char') || 
            str_contains($type, 'varchar')  || 
            str_contains($type, 'text')
        ) return 'string';
        if(str_contains($type, 'int')) return 'int';
        if(
            str_contains($type, 'decimal') ||
            str_contains($type, 'float') ||
            str_contains($type, 'double')
        ) return 'float';
        if(
            str_contains($type, 'datetime') ||
            str_contains($type, 'timestamp') ||
            str_contains($type, 'date')
        ) return 'datetime';
        return '';
    }

    /**
     * Función que calcula los límites inferior y superior de un tipo de
     * datos.
     * string: El límite inferior siempre es 0
     * int: Depende del tipo de datos definido en la BD tendrá unos límites u otros.
     * float: Los tipos float y double no se comprueban.
     * decimal: decimal(4, 2)
     *
     * @param string $type Hay que pasarle el campo Type que devuelve DESCRIBE
     * @return array
     */
    public static function getLimitsType(string $type): array {
        if(str_contains($type, 'varchar')) return ['min' => 0, 'max' => self::getNumberInParents($type)];
        if(str_contains($type, 'char')){
            $size = self::getNumberInParents($type);
            return ['min' => $size, 'max' => $size];
        }
        if(str_contains($type, 'text')) return ['min' => 0, 'max' => 65535];

        $unsigned = str_contains($type, 'unsigned');
        // 1 byte
        if(str_contains($type, 'tinyint')) return $unsigned ? ['min' => 0, 'max' => 255] : ['min' => -128, 'max' => 127];
        // 2 bytes
        if(str_contains($type, 'smallint')) return $unsigned ? ['min' => 0, 'max' => 65535] : ['min' => -32768, 'max' => 32767];
        // 3 bytes
        if(str_contains($type, 'mediumint')) return $unsigned ? ['min' => 0, 'max' => 16777215] : ['min' =>  	-8388608, 'max' => 8388607];
        // 8 bytes
        if(str_contains($type, 'bigint')) return $unsigned ? ['min' => 0, 'max' => 18446744073709551615] : ['min' => -9223372036854775808, 'max' => 9223372036854775807];
        // 4 bytes
        if(str_contains($type, 'int')) return $unsigned ? ['min' => 0, 'max' => 4294967295] : ['min' =>  -2147483648, 'max' => 2147483647];
        if(str_contains($type, 'decimal')){
            $num = self::getDecimalNumber($type);
            return ['min' =>  -$num, 'max' => $num];
        }
        return [];
    }

    /**
     * Undocumented function
     *
     * @param string $type
     * @return integer
     */
    private static function getNumberInParents(string $type): int {
        $open = strpos($type, '(') + 1;
        $close = strpos($type, ')');
        $subcadena = substr($type, $open, $close - $open);
        $number = (int) $subcadena;
        return $number;
    }

    public static function getDecimalNumber(string $type): float {
        // Precision
        $open = strpos($type, '(') + 1;
        $close = strpos($type, ',');
        $subcadena = substr($type, $open, $close - $open);
        $precision = (int) $subcadena;
        // Scale
        $open = strpos($type, ',') + 1;
        $close = strpos($type, ')');
        $subcadena = substr($type, $open, $close - $open);
        $scale = (int) $subcadena;
        $num = '';
        for($i=0; $i < $precision - $scale; $i++){
            $num .= '9';
        }
        $num .= '.';
        for($i=0; $i < $scale; $i++){
            $num .= '9';
        }
        return (float) $num;
    }
}