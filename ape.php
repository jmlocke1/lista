<?php
require_once __DIR__ . "/vendor/autoload.php";

use duncan3dc\MetaAudio\Tagger;

$tagger = new Tagger;
$tagger->addDefaultModules();
$king = "D:/Prueba/King Crimson - Beat (1982) 30th anniversary edition eac.ape.cue (paranoid mode 100% quality rip, original & fresh cd) by felafalos/CDImage.ape";
$michael = "D:/Prueba/Michael Jackson - Thriller/01.Wanna Be Startin' Somethin'.flac";
$mp3 = $tagger->open($michael);
$info = [];
$info[] = $mp3->getArtist();
$info[] = $mp3->getAlbum();
$info[] = $mp3->getYear();
$info[] = $mp3->getTrackNumber();
$info[] = $mp3->getTitle();

var_dump($info);