<?php
namespace App\helpers;
use wapmorgan\Mp3Info\Mp3Info;

class Mp3InfoHelper {
    public Mp3Info $info;
    public $song;
    /**
     * The ‘Lead artist/Lead performer/Soloist/Performing group’ is used for the main artist.
     */
    public $artist;
    /**
     * The ‘Band/Orchestra/Accompaniment’ frame is used for additional information about the performers in the recording.
     */
    public $tpe2;
    /**
     * The ‘Conductor’ frame is used for the name of the conductor.
     */
    public $tpe3;
    /**
     * The ‘Interpreted, remixed, or otherwise modified by’ frame contains more information about the people behind a remix and similar interpretations of another existing piece.
     */
    public $tpe4;
    /**
     * The ‘Original artist/performer’ frame is intended for the performer of the original recording, if for example the music in the file should be a cover of a previously released song.
     */
    public $tope;
    public $album;
    /**
     * The ‘Composer’ frame is intended for the name of the composer.
     */
    public $composer;
    public $year;
    public $comment;
    public $track;
    /**
     * En la especificación ID3v1 se almacenaba como un número, que está almacenado en la tabla genre, junto con el nombre y un enlace a wikipedia. 
     * En ID3v2 se almacena como una cadena
     */
    public $genrev1;
    public string $genrev2;

    public function __construct(Mp3Info $info)
    {
        $this->info = $info;
        $this->song = (!empty($info->tags2['TIT2']) ? $info->tags2['TIT2'] : (!empty($info->tags1['song']) ? $info->tags1['song'] : (!empty($info->tags['song']) ? $info->tags['song'] : '')));
        $this->artist = (!empty($info->tags2['TPE1']) ? $info->tags2['TPE1'] : (!empty($info->tags1['artist']) ? $info->tags1['artist'] : (!empty($info->tags['artist']) ? $info->tags['artist'] : '')));
        $this->tpe2 = $info->tags2['TPE2'] ?? "";
        $this->tpe3 = $info->tags2['TPE3'] ?? "";
        $this->tpe4 = $info->tags2['TPE4'] ?? "";
        $this->tope = $info->tags2['TOPE'] ?? "";
        $this->album = (!empty($info->tags2['TALB']) ? $info->tags2['TALB'] : (!empty($info->tags1['album']) ? $info->tags1['album'] : (!empty($info->tags['album']) ? $info->tags['album'] : '')));
        $this->composer = $info->tags2['TCOM'] ?? "";
        $this->year = (!empty($info->tags2['TYER']) ? $info->tags2['TYER'] : (!empty($info->tags1['year']) ? $info->tags1['year'] : (!empty($info->tags['year']) ? $info->tags['year'] : '')));
        $this->comment = (!empty($info->tags1['comment']) ? $info->tags1['comment'] : (!empty($info->tags['comment']) ? $info->tags['comment'] : ($this->setComment($info))));
        $this->track = (!empty($info->tags2['TRCK']) ? $info->tags2['TRCK'] : (!empty($info->tags1['track']) ? $info->tags1['track'] : (!empty($info->tags['track']) ? $info->tags['track'] : '')));
        $this->genrev1 = ($info->tags1['genre'] ?? '');
        $this->genrev2 = (!empty($info->tags2['TCON']) ? $info->tags2['TCON'] : (!empty($info->tags['genre']) ? $info->tags['genre'] : ''));
    }

    public function setSong($info){
        $tags1 = $info->tags1;
    }

    public function setComment($info){
        $comment = $info->tags2['COMM'] ?? [];
        $text = "";
        foreach ($comment as $lang => $value) {
            if(empty($value['short']) && empty($value['actual'])) continue;
            $text .= $lang . ": ".json_encode($value);
        }
        return $text;
    }
}