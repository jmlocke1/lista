<?php
namespace Model;

class Album extends \Model\ActiveRecord {
	const TABLENAME = 'album';
	protected static $columnsDB=['id', 'album_name', 'subtitle', 'folder_name', 'artist', 'num_tracks', 'descripcion', 'publicado', 'ubicacion', 'num_ubicacion', 'parent', 'created_at', 'updated_at'];
	public static $foreignKeys = [
		[
			"COLUMN_NAME" => "parent",
			"REFERENCED_TABLE_NAME" => "album",
			"REFERENCED_COLUMN_NAME" => "id"		
		],
		[
			"COLUMN_NAME" => "ubicacion",
			"REFERENCED_TABLE_NAME" => "ubicacion",
			"REFERENCED_COLUMN_NAME" => "id"		
		]	
	];

	protected static $getFunctions = [];
	protected static $setFunctions = [
		'id' => 'setId',
		'album_name' => 'setAlbumName',
		'subtitle' => 'setSubtitle',
		'folder_name' => 'setFolderName',
		'artist' => 'setArtist',
		'num_tracks' => 'setNumTracks',
		'descripcion' => 'setDescripcion',
		'publicado' => 'setPublicado',
		'ubicacion' => 'setUbicacion',
		'num_ubicacion' => 'setNumUbicacion',
		'parent' => 'setParent',
		'created_at' => 'setCreatedAt',
		'updated_at' => 'setUpdatedAt'
	];

	public function __construct($args = []){
		$this->id = $args['id'] ?? null;
		$this->album_name = $args['album_name'] ?? null;
		$this->subtitle = $args['subtitle'] ?? null;
		$this->folder_name = $args['folder_name'] ?? null;
		$this->artist = $args['artist'] ?? null;
		$this->num_tracks = $args['num_tracks'] ?? null;
		$this->descripcion = $args['descripcion'] ?? null;
		$this->publicado = $args['publicado'] ?? null;
		$this->ubicacion = $args['ubicacion'] ?? null;
		$this->num_ubicacion = $args['num_ubicacion'] ?? null;
		$this->parent = $args['parent'] ?? null;
		$this->created_at = $args['created_at'] ?? null;
		$this->updated_at = $args['updated_at'] ?? null;
	}

	// Métodos Set
	protected function setId(int $id) {
		if($id >= -2147483648 && $id <= 2147483647){
			$this->data['id'] = $id;
		}else{
			$this->setAlert('error', 'El valor asignado al campo id está fuera del rango admitido (-2147483648, 2147483647)');
		}
	}

	protected function setAlbumName(string $album_name) {
		$length = strlen($album_name);
		if($length >= 0 && $length <= 255){
			$this->data['album_name'] = $album_name;
		}else{
			$this->setAlert('error', 'El texto supera la capacidad máxima del campo (0, 255)');
		}
	}

	protected function setSubtitle(string $subtitle) {
		$length = strlen($subtitle);
		if($length >= 0 && $length <= 255){
			$this->data['subtitle'] = $subtitle;
		}else{
			$this->setAlert('error', 'El texto supera la capacidad máxima del campo (0, 255)');
		}
	}

	protected function setFolderName(string $folder_name) {
		$length = strlen($folder_name);
		if($length >= 0 && $length <= 1024){
			$this->data['folder_name'] = $folder_name;
		}else{
			$this->setAlert('error', 'El texto supera la capacidad máxima del campo (0, 1024)');
		}
	}

	protected function setArtist(string $artist) {
		$length = strlen($artist);
		if($length >= 0 && $length <= 255){
			$this->data['artist'] = $artist;
		}else{
			$this->setAlert('error', 'El texto supera la capacidad máxima del campo (0, 255)');
		}
	}

	protected function setNumTracks(int $num_tracks) {
		if($num_tracks >= 0 && $num_tracks <= 255){
			$this->data['num_tracks'] = $num_tracks;
		}else{
			$this->setAlert('error', 'El valor asignado al campo num_tracks está fuera del rango admitido (0, 255)');
		}
	}

	protected function setDescripcion(string $descripcion) {
		$length = strlen($descripcion);
		if($length >= 0 && $length <= 3000){
			$this->data['descripcion'] = $descripcion;
		}else{
			$this->setAlert('error', 'El texto supera la capacidad máxima del campo (0, 3000)');
		}
	}

	protected function setPublicado(string $publicado) {
		$this->setDateTime('publicado', $publicado);
	}

	protected function setUbicacion(int $ubicacion) {
		if($ubicacion >= -2147483648 && $ubicacion <= 2147483647){
			$this->data['ubicacion'] = $ubicacion;
		}else{
			$this->setAlert('error', 'El valor asignado al campo ubicacion está fuera del rango admitido (-2147483648, 2147483647)');
		}
	}

	protected function setNumUbicacion(int $num_ubicacion) {
		if($num_ubicacion >= -2147483648 && $num_ubicacion <= 2147483647){
			$this->data['num_ubicacion'] = $num_ubicacion;
		}else{
			$this->setAlert('error', 'El valor asignado al campo num_ubicacion está fuera del rango admitido (-2147483648, 2147483647)');
		}
	}

	protected function setParent(int $parent) {
		if($parent >= -2147483648 && $parent <= 2147483647){
			$this->data['parent'] = $parent;
		}else{
			$this->setAlert('error', 'El valor asignado al campo parent está fuera del rango admitido (-2147483648, 2147483647)');
		}
	}

	protected function setCreatedAt(string $created_at) {
		$this->setDateTime('created_at', $created_at);
	}

	protected function setUpdatedAt(string $updated_at) {
		$this->setDateTime('updated_at', $updated_at);
	}


	// Métodos particulares del modelo
}