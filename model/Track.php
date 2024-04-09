<?php
namespace Model;

class Track extends \Model\ActiveRecord {
	const TABLENAME = 'track';
	protected static $columnsDB=['id', 'album_id', 'filename', 'song', 'artist', 'tpe2', 'tpe3', 'tpe4', 'tope', 'album', 'composer', 'year', 'comment', 'track', 'genrev1', 'genrev2', 'codecversion', 'layerversion', 'audiosize', 'duration', 'bitrate', 'samplerate', 'hascover', 'channel'];

	protected static $getFunctions = [];
	protected static $setFunctions = [
		'id' => 'setId',
		'album_id' => 'setAlbumId',
		'filename' => 'setFilename',
		'song' => 'setSong',
		'artist' => 'setArtist',
		'tpe2' => 'setTpe2',
		'tpe3' => 'setTpe3',
		'tpe4' => 'setTpe4',
		'tope' => 'setTope',
		'album' => 'setAlbum',
		'composer' => 'setComposer',
		'year' => 'setYear',
		'comment' => 'setComment',
		'track' => 'setTrack',
		'genrev1' => 'setGenrev1',
		'genrev2' => 'setGenrev2',
		'codecversion' => 'setCodecversion',
		'layerversion' => 'setLayerversion',
		'audiosize' => 'setAudiosize',
		'duration' => 'setDuration',
		'bitrate' => 'setBitrate',
		'samplerate' => 'setSamplerate',
		'hascover' => 'setHascover',
		'channel' => 'setChannel'
	];

	public function __construct($args = []){
		$this->id = $args['id'] ?? null;
		$this->album_id = $args['album_id'] ?? null;
		$this->filename = $args['filename'] ?? null;
		$this->song = $args['song'] ?? null;
		$this->artist = $args['artist'] ?? null;
		$this->tpe2 = $args['tpe2'] ?? null;
		$this->tpe3 = $args['tpe3'] ?? null;
		$this->tpe4 = $args['tpe4'] ?? null;
		$this->tope = $args['tope'] ?? null;
		$this->album = $args['album'] ?? null;
		$this->composer = $args['composer'] ?? null;
		$this->year = $args['year'] ?? null;
		$this->comment = $args['comment'] ?? null;
		$this->track = $args['track'] ?? null;
		$this->genrev1 = $args['genrev1'] ?? null;
		$this->genrev2 = $args['genrev2'] ?? null;
		$this->codecversion = $args['codecversion'] ?? null;
		$this->layerversion = $args['layerversion'] ?? null;
		$this->audiosize = $args['audiosize'] ?? null;
		$this->duration = $args['duration'] ?? null;
		$this->bitrate = $args['bitrate'] ?? null;
		$this->samplerate = $args['samplerate'] ?? null;
		$this->hascover = $args['hascover'] ?? null;
		$this->channel = $args['channel'] ?? null;
	}

	// Métodos Set
	protected function setId(int $id) {
		if($id >= -2147483648 && $id <= 2147483647){
			$this->data['id'] = $id;
		}else{
			$this->setAlert('error', 'El valor asignado al campo id está fuera del rango admitido (-2147483648, 2147483647)');
		}
	}

	protected function setAlbumId(int $album_id) {
		if($album_id >= -2147483648 && $album_id <= 2147483647){
			$this->data['album_id'] = $album_id;
		}else{
			$this->setAlert('error', 'El valor asignado al campo album_id está fuera del rango admitido (-2147483648, 2147483647)');
		}
	}

	protected function setFilename(string $filename) {
		$length = strlen($filename);
		if($length >= 0 && $length <= 1024){
			$this->data['filename'] = $filename;
		}else{
			$this->setAlert('error', 'El texto supera la capacidad máxima del campo (0, 1024)');
		}
	}

	protected function setSong(string $song) {
		$length = strlen($song);
		if($length >= 0 && $length <= 512){
			$this->data['song'] = $song;
		}else{
			$this->setAlert('error', 'El texto supera la capacidad máxima del campo (0, 512)');
		}
	}

	protected function setArtist(string $artist) {
		$length = strlen($artist);
		if($length >= 0 && $length <= 512){
			$this->data['artist'] = $artist;
		}else{
			$this->setAlert('error', 'El texto supera la capacidad máxima del campo (0, 512)');
		}
	}

	protected function setTpe2(string $tpe2) {
		$length = strlen($tpe2);
		if($length >= 0 && $length <= 512){
			$this->data['tpe2'] = $tpe2;
		}else{
			$this->setAlert('error', 'El texto supera la capacidad máxima del campo (0, 512)');
		}
	}

	protected function setTpe3(string $tpe3) {
		$length = strlen($tpe3);
		if($length >= 0 && $length <= 512){
			$this->data['tpe3'] = $tpe3;
		}else{
			$this->setAlert('error', 'El texto supera la capacidad máxima del campo (0, 512)');
		}
	}

	protected function setTpe4(string $tpe4) {
		$length = strlen($tpe4);
		if($length >= 0 && $length <= 512){
			$this->data['tpe4'] = $tpe4;
		}else{
			$this->setAlert('error', 'El texto supera la capacidad máxima del campo (0, 512)');
		}
	}

	protected function setTope(string $tope) {
		$length = strlen($tope);
		if($length >= 0 && $length <= 255){
			$this->data['tope'] = $tope;
		}else{
			$this->setAlert('error', 'El texto supera la capacidad máxima del campo (0, 255)');
		}
	}

	protected function setAlbum(string $album) {
		$length = strlen($album);
		if($length >= 0 && $length <= 512){
			$this->data['album'] = $album;
		}else{
			$this->setAlert('error', 'El texto supera la capacidad máxima del campo (0, 512)');
		}
	}

	protected function setComposer(string $composer) {
		$length = strlen($composer);
		if($length >= 0 && $length <= 512){
			$this->data['composer'] = $composer;
		}else{
			$this->setAlert('error', 'El texto supera la capacidad máxima del campo (0, 512)');
		}
	}

	protected function setYear( $year) {

	}

	protected function setComment(string $comment) {
		$length = strlen($comment);
		if($length >= 0 && $length <= 512){
			$this->data['comment'] = $comment;
		}else{
			$this->setAlert('error', 'El texto supera la capacidad máxima del campo (0, 512)');
		}
	}

	protected function setTrack(int $track) {
		if($track >= -2147483648 && $track <= 2147483647){
			$this->data['track'] = $track;
		}else{
			$this->setAlert('error', 'El valor asignado al campo track está fuera del rango admitido (-2147483648, 2147483647)');
		}
	}

	protected function setGenrev1(int $genrev1) {
		if($genrev1 >= -2147483648 && $genrev1 <= 2147483647){
			$this->data['genrev1'] = $genrev1;
		}else{
			$this->setAlert('error', 'El valor asignado al campo genrev1 está fuera del rango admitido (-2147483648, 2147483647)');
		}
	}

	protected function setGenrev2(string $genrev2) {
		$length = strlen($genrev2);
		if($length >= 0 && $length <= 255){
			$this->data['genrev2'] = $genrev2;
		}else{
			$this->setAlert('error', 'El texto supera la capacidad máxima del campo (0, 255)');
		}
	}

	protected function setCodecversion(int $codecversion) {
		if($codecversion >= 0 && $codecversion <= 255){
			$this->data['codecversion'] = $codecversion;
		}else{
			$this->setAlert('error', 'El valor asignado al campo codecversion está fuera del rango admitido (0, 255)');
		}
	}

	protected function setLayerversion(int $layerversion) {
		if($layerversion >= 0 && $layerversion <= 255){
			$this->data['layerversion'] = $layerversion;
		}else{
			$this->setAlert('error', 'El valor asignado al campo layerversion está fuera del rango admitido (0, 255)');
		}
	}

	protected function setAudiosize(int $audiosize) {
		if($audiosize >= -2147483648 && $audiosize <= 2147483647){
			$this->data['audiosize'] = $audiosize;
		}else{
			$this->setAlert('error', 'El valor asignado al campo audiosize está fuera del rango admitido (-2147483648, 2147483647)');
		}
	}

	protected function setDuration(float $duration) {
		$this->data['duration'] = $duration;
	}

	protected function setBitrate(int $bitrate) {
		if($bitrate >= -128 && $bitrate <= 127){
			$this->data['bitrate'] = $bitrate;
		}else{
			$this->setAlert('error', 'El valor asignado al campo bitrate está fuera del rango admitido (-128, 127)');
		}
	}

	protected function setSamplerate(int $samplerate) {
		if($samplerate >= -2147483648 && $samplerate <= 2147483647){
			$this->data['samplerate'] = $samplerate;
		}else{
			$this->setAlert('error', 'El valor asignado al campo samplerate está fuera del rango admitido (-2147483648, 2147483647)');
		}
	}

	protected function setHascover(int $hascover) {
		if($hascover >= -128 && $hascover <= 127){
			$this->data['hascover'] = $hascover;
		}else{
			$this->setAlert('error', 'El valor asignado al campo hascover está fuera del rango admitido (-128, 127)');
		}
	}

	protected function setChannel(string $channel) {
		$length = strlen($channel);
		if($length >= 0 && $length <= 60){
			$this->data['channel'] = $channel;
		}else{
			$this->setAlert('error', 'El texto supera la capacidad máxima del campo (0, 60)');
		}
	}


	// Métodos particulares del modelo
}