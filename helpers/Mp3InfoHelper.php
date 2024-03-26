<?php
namespace App\helper;
use wapmorgan\Mp3Info\Mp3Info;

class Mp3InfoHelper {
    public Mp3Info $info;
    public $song;
    public $artist;
    public $album;
    public $year;
    public $comment;
    public $track;
    public $genre;

    public function __construct(Mp3Info $info)
    {
        $this->info = $info;
        $this->song = (!empty($info->tags2['TIT2']) ? $info->tags2['TIT2'] : (!empty($info->tags1['song']) ? $info->tags1['song'] : (!empty($info->tags['song']) ? $info->tags['song'] : '')));
        $this->artist = (!empty($info->tags2['TPE1']) ? $info->tags2['TPE1'] : (!empty($info->tags1['artist']) ? $info->tags1['artist'] : (!empty($info->tags['artist']) ? $info->tags['artist'] : '')));
        $this->album = (!empty($info->tags2['TALB']) ? $info->tags2['TALB'] : (!empty($info->tags1['album']) ? $info->tags1['album'] : (!empty($info->tags['album']) ? $info->tags['album'] : '')));
        $this->year = (!empty($info->tags2['TYER']) ? $info->tags2['TYER'] : (!empty($info->tags1['year']) ? $info->tags1['year'] : (!empty($info->tags['year']) ? $info->tags['year'] : '')));
        $this->comment = (!empty($info->tags1['comment']) ? $info->tags1['comment'] : (!empty($info->tags['comment']) ? $info->tags['comment'] : ($this->setComment($info))));
        $this->track = (!empty($info->tags2['TRCK']) ? $info->tags2['TRCK'] : (!empty($info->tags1['track']) ? $info->tags1['track'] : (!empty($info->tags['track']) ? $info->tags['track'] : '')));
        $this->genre = (!empty($info->tags2['TCON']) ? $info->tags2['TCON'] : (!empty($info->tags1['genre']) ? $info->tags1['genre'] : (!empty($info->tags['genre']) ? $info->tags['genre'] : '')));
    }

    public function setSong($info){
        $tags1 = $info->tags1;
    }

    public function setComment($info){
        $comment = $info->tags2['COMM'];
        $text = "";
        foreach ($comment as $lang => $value) {
            if(empty($value['short']) && empty($value['actual'])) continue;
            $text .= $lang . ": ".json_encode($value);
        }
        return $text;
    }
}