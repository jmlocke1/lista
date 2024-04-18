<?php
namespace App\config;

class Config {
    /**
     * Servidor de la base de datos
     */
    const DB_HOST = ConfigLocal::DB_HOST;
    /**
     * Nombre de la base de datos
     */
    const DB_NAME = ConfigLocal::DB_NAME;
    /**
     * Usuario de la base de datos
     */
    const DB_USERNAME = ConfigLocal::DB_USERNAME;
    /**
     * Contraseña del usuario de la base de datos
     */
    const DB_PASSWORD = ConfigLocal::DB_PASSWORD;
    /**
     * Definimos la codificación de los caracteres
     */
    const DB_ENCODE = 'utf8';
    /**
     * Zona horaria para los logs
     */
    const LOG_TIME_ZONE = "Europe/Madrid";
    
	const DIR_IMG_PRINCIPAL = '/build/img/principal/';
	const ABSOLUTE_DIR_IMG_PRINCIPAL = DIR_ROOT . self::DIR_IMG_PRINCIPAL;
    const IMAGE_TYPES = ['jpg', 'webp', 'avif'];
    /**
	 * Páginas que contiene esta aplicación. Se usan aquí para poner
	 * una imagen de cabecera diferente cada día
	 */
	const PAGES = [
		'index.php',
        'musica.php'
	];
}