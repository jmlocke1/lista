<?php
require DIR_ROOT.'/templates/header.php';
?>
<main class="contenedor">
        <h1 class="centrar-texto">Formulario de música</h1>
        
        <form class="formulario" action="mp3-save.php" method="post">
            <fieldset>
                <legend>Album: The Great Paris Concert CD1</legend>

                <div class="campo">
                    <label class="campo__label" for="album[0]['album_name']">Nombre Album</label>
                    <input class="campo__field" ype="text" name="album[0]['album_name']" value="The Great Paris Concert">
                </div>
                <div class="campo">
                    <label class="campo__label" for="album[0]['subtitle']">Subtítulo Album</label>
                    <input class="campo__field" type="text" name="album[0]['subtitle']" value="CD 1">
                </div>
                <div class="campo">
                    <label class="campo__label" for="album[0]['artist']">Artista</label>
                    <input class="campo__field" type="text" name="album[0]['artist']" value="Duke Ellington">
                </div>
                <div class="campo">
                    <label class="campo__label" for="album[0]['folder_name']">Directorio</label>
                    <input class="campo__field" type="text" name="album[0]['folder_name']" value="1963 - The Great Paris Concert CD1">
                </div>
                <?php 
                $published = date('Y-m-d', strtotime('1973-01-01'));
                ?>
                <div class="campo">
                    <label class="campo__label" for="album[0]['publicado']">Publicado</label>
                    <input class="campo__field" type="date" name="album[0]['publicado']" value="<?= $published; ?>">
                    <label class="campo__label" for="album[0]['num_tracks']">Número de tracks</label>
                    <input class="campo__field" type="number" name="album[0]['num_tracks']" value="10">
                </div>
                <div class="campo">
                    <label class="campo__label" for="album[0]['ubicacion']" title="">Ubicación</label>
                    <input class="campo__field" type="text" name="album[0]['ubicacion']" value="MP3">
                    <label class="campo__label" for="album[0][num_ubicacion]">Núm. ubicación</label>
                    <input class="campo__field" type="number" name="album[0][num_ubicacion]" value="10">
                </div>
                
                <div class="campo">
                    <label class="campo__label" for="album[0]['description']">Descripción</label>
                    <textarea class="campo__field campo__field--textarea" name="album[0]['description']" >1963 - The Great Paris Concert CD1</textarea>
                </div>
                
                <div class="songs">
                    <div class="campo">
                        <label class="campo__label" for="album[0]['tracks'][1]['filename']">Filename</label>
                        <input class="campo__field" type="text" name="album[0]['tracks'][1]['filename']" value="01 - Kinda Dukish.mp3">
                    </div>
                    <div class="campo">
                        <label class="campo__label" for="album[0]['tracks'][1]['song']">Canción</label>
                        <input class="campo__field" type="text" name="album[0]['tracks'][1]['song']" value="Kinda Dukish">
                    </div>
                    <div class="campo">
                        <label class="campo__label" for="album[0]['tracks'][1]['artist']">Artista</label>
                        <input class="campo__field" type="text" name="album[0]['tracks'][1]['artist']" value="Duke Ellington">
                    </div>
                    <div class="campo" title="The ‘Band/Orchestra/Accompaniment’ frame is used for additional information about the performers in the recording">
                        <label class="campo__label" for="album[0]['tracks'][1]['tpe2']">Tpe2</label>
                        <input class="campo__field" type="text" name="album[0]['tracks'][1]['tpe2']" value="Duke Ellington & his Orchestra">
                    </div>
                    <div class="campo" title="Director de la orquesta.">
                        <label class="campo__label" for="album[0]['tracks'][1]['tpe3']">Tpe3</label>
                        <input class="campo__field" type="text" name="album[0]['tracks'][1]['tpe3']" value="Duke Ellington">
                    </div>
                    <div class="campo" title="El cuadro 'Interpretado, remezclado o modificado de otro modo por' contiene más información sobre las personas detrás de un remix e interpretaciones similares de otra pieza existente.">
                        <label class="campo__label" for="album[0]['tracks'][1]['tpe4']">Tpe4</label>
                        <input class="campo__field" type="text" name="album[0]['tracks'][1]['tpe4']" value="Remezclado por Duke Ellington">
                    </div>
                    <div class="campo" title="El cuadro 'Artista/intérprete original' está destinado al intérprete de la grabación original, si, por ejemplo, la música del archivo debe ser una versión de una canción publicada anteriormente.">
                        <label class="campo__label" for="album[0]['tracks'][1]['tope']">Tope</label>
                        <input class="campo__field" type="text" name="album[0]['tracks'][1]['tope']" value="Versión original de Duke Ellington">
                    </div>
                    <div class="campo">
                        <label class="campo__label" for="album[0]['tracks'][1]['album']">Álbum</label>
                        <input class="campo__field" type="text" name="album[0]['tracks'][1]['album']" value="The Great Paris Concert">
                    </div>
                    <div class="campo">
                        <label class="campo__label" for="album[0]['tracks'][1]['composer']">Compositor</label>
                        <input class="campo__field" type="text" name="album[0]['tracks'][1]['composer']" value="Duke Ellington">
                    </div>
                    <div class="campo">
                        <label class="campo__label" for="album[0]['tracks'][1]['comment']">Comentario</label>
                        <input class="campo__field" type="text" name="album[0]['tracks'][1]['comment']" value="Rip with EAC">
                    </div>
                    <div class="campo">
                        <label class="campo__label" for="album[0]['tracks'][1]['year']">Año</label>
                        <input class="campo__field" type="number" name="album[0]['tracks'][1]['year']" value="1963">
                        <label class="campo__label" for="album[0]['tracks'][1]['track']">Track</label>
                        <input class="campo__field" type="number" name="album[0]['tracks'][1]['track']" value="1">
                    </div>
                    <div class="campo">
                        <label class="campo__label" for="album[0]['tracks'][1]['genrev1']">GenreV1</label>
                        <input class="campo__field" type="number" name="album[0]['tracks'][1]['genrev1']" value="8">
                        <label class="campo__label" for="album[0]['tracks'][1]['genrev2']">GenreV2</label>
                        <input class="campo__field" type="text" name="album[0]['tracks'][1]['genrev2']" value="Jazz">
                    </div>
                    <div class="campo">
                        <label class="campo__label" for="album[0]['tracks'][1]['codecversion']">codecversion</label>
                        <input class="campo__field" type="number" name="album[0]['tracks'][1]['codecversion']" value="1">
                        <label class="campo__label" for="album[0]['tracks'][1]['layerversion']">layerversion</label>
                        <input class="campo__field" type="number" name="album[0]['tracks'][1]['layerversion']" value="3">
                    </div>
                    <div class="campo">
                        <label class="campo__label" for="album[0]['tracks'][1]['audiosize']">Tamaño Audio</label>
                        <input class="campo__field" type="number" name="album[0]['tracks'][1]['audiosize']" value="3049756">
                        <label class="campo__label" for="album[0]['tracks'][1]['duration']">Duración</label>
                        <input class="campo__field" type="number" name="album[0]['tracks'][1]['duration']" value="111.93469387755">
                    </div>
                    <div class="campo">
                        <label class="campo__label" for="album[0]['tracks'][1]['bitrate']">Bitrate</label>
                        <input class="campo__field" type="number" name="album[0]['tracks'][1]['bitrate']" value="217915.95310313">
                        <label class="campo__label" for="album[0]['tracks'][1]['samplerate']">Samplerate</label>
                        <input class="campo__field" type="number" name="album[0]['tracks'][1]['samplerate']" value="44100">
                    </div>
                    <div class="campo">
                        <label class="campo__label" for="album[0]['tracks'][1]['hascover']">Tiene portada</label>
                        <input class="campo__field" type="checkbox" name="album[0]['tracks'][1]['hascover']" >
                        <label class="campo__label" for="album[0]['tracks'][1]['channel']">Channel</label>
                        <input class="campo__field" type="text" name="album[0]['tracks'][1]['channel']" value="joint_stereo">
                    </div>
                </div>
                <div class="songs">
                    <div class="campo">
                        <label class="campo__label" for="album[0]['tracks'][2]['filename']">Filename</label>
                        <input class="campo__field" type="text" name="album[0]['tracks'][2]['filename']" value="02 - Rockin' In Rhythm.mp3">
                    </div>
                    <div class="campo">
                        <label class="campo__label" for="album[0]['tracks'][2]['song']">Canción</label>
                        <input class="campo__field" type="text" name="album[0]['tracks'][2]['song']" value="Rockin' In Rhythm">
                    </div>
                    <div class="campo">
                        <label class="campo__label" for="album[0]['tracks'][2]['artist']">Artista</label>
                        <input class="campo__field" type="text" name="album[0]['tracks'][2]['artist']" value="Duke Ellington">
                    </div>
                    <div class="campo" title="The ‘Band/Orchestra/Accompaniment’ frame is used for additional information about the performers in the recording">
                        <label class="campo__label" for="album[0]['tracks']21]['tpe2']">Tpe2</label>
                        <input class="campo__field" type="text" name="album[0]['tracks'][2]['tpe2']" value="Duke Ellington & his Orchestra">
                    </div>
                    <div class="campo" title="Director de la orquesta.">
                        <label class="campo__label" for="album[0]['tracks'][2]['tpe3']">Tpe3</label>
                        <input class="campo__field" type="text" name="album[0]['tracks'][2]['tpe3']" value="Duke Ellington">
                    </div>
                    <div class="campo" title="El cuadro 'Interpretado, remezclado o modificado de otro modo por' contiene más información sobre las personas detrás de un remix e interpretaciones similares de otra pieza existente.">
                        <label class="campo__label" for="album[0]['tracks'][2]['tpe4']">Tpe4</label>
                        <input class="campo__field" type="text" name="album[0]['tracks'][2]['tpe4']" value="Remezclado por Duke Ellington">
                    </div>
                    <div class="campo" title="El cuadro 'Artista/intérprete original' está destinado al intérprete de la grabación original, si, por ejemplo, la música del archivo debe ser una versión de una canción publicada anteriormente.">
                        <label class="campo__label" for="album[0]['tracks'][2]['tope']">Tope</label>
                        <input class="campo__field" type="text" name="album[0]['tracks'][2]['tope']" value="Versión original de Duke Ellington">
                    </div>
                    <div class="campo">
                        <label class="campo__label" for="album[0]['tracks'][2]['album']">Álbum</label>
                        <input class="campo__field" type="text" name="album[0]['tracks'][2]['album']" value="The Great Paris Concert">
                    </div>
                    <div class="campo">
                        <label class="campo__label" for="album[0]['tracks'][2]['composer']">Compositor</label>
                        <input class="campo__field" type="text" name="album[0]['tracks'][2]['composer']" value="Duke Ellington">
                    </div>
                    <div class="campo">
                        <label class="campo__label" for="album[0]['tracks'][2]['comment']">Comentario</label>
                        <input class="campo__field" type="text" name="album[0]['tracks'][2]['comment']" value="Rip with EAC">
                    </div>
                    <div class="campo">
                        <label class="campo__label" for="album[0]['tracks'][2]['year']">Año</label>
                        <input class="campo__field" type="number" name="album[0]['tracks'][2]['year']" value="1963">
                        <label class="campo__label" for="album[0]['tracks'][2]['track']">Track</label>
                        <input class="campo__field" type="number" name="album[0]['tracks'][2]['track']" value="2">
                    </div>
                    <div class="campo">
                        <label class="campo__label" for="album[0]['tracks'][2]['genrev1']">GenreV1</label>
                        <input class="campo__field" type="number" name="album[0]['tracks'][2]['genrev1']" value="8">
                        <label class="campo__label" for="album[0]['tracks'][2]['genrev2']">GenreV2</label>
                        <input class="campo__field" type="text" name="album[0]['tracks'][2]['genrev2']" value="Jazz">
                    </div>
                    <div class="campo">
                        <label class="campo__label" for="album[0]['tracks'][2]['codecversion']">codecversion</label>
                        <input class="campo__field" type="number" name="album[0]['tracks'][2]['codecversion']" value="1">
                        <label class="campo__label" for="album[0]['tracks'][2]['layerversion']">layerversion</label>
                        <input class="campo__field" type="number" name="album[0]['tracks'][2]['layerversion']" value="3">
                    </div>
                    <div class="campo">
                        <label class="campo__label" for="album[0]['tracks'][2]['audiosize']">Tamaño Audio</label>
                        <input class="campo__field" type="number" name="album[0]['tracks'][2]['audiosize']" value="6964954">
                        <label class="campo__label" for="album[0]['tracks'][2]['duration']">Duración</label>
                        <input class="campo__field" type="number" name="album[0]['tracks'][2]['duration']" value="227.34367346939">
                    </div>
                    <div class="campo">
                        <label class="campo__label" for="album[0]['tracks'][2]['bitrate']">Bitrate</label>
                        <input class="campo__field" type="number" name="album[0]['tracks'][2]['bitrate']" value="245061.71444164">
                        <label class="campo__label" for="album[0]['tracks'][2]['samplerate']">Samplerate</label>
                        <input class="campo__field" type="number" name="album[0]['tracks'][2]['samplerate']" value="44100">
                    </div>
                    <div class="campo">
                        <label class="campo__label" for="album[0]['tracks'][2]['hascover']">Tiene portada</label>
                        <input class="campo__field" type="checkbox" name="album[0]['tracks'][2]['hascover']" >
                        <label class="campo__label" for="album[0]['tracks'][2]['channel']">Channel</label>
                        <input class="campo__field" type="text" name="album[0]['tracks'][2]['channel']" value="joint_stereo">
                    </div>
                </div>

            </fieldset>
            <div class="mt-2">
                <input class="boton__principal " type="submit" value="Enviar">
                <a class="boton__secundario " src="/musica">Volver a Música</a>
            </div>
            
        </form>
    </main>

<?php require DIR_ROOT.'/templates/footer.php' ?>