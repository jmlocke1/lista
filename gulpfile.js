const { src, dest, watch, parallel, series } = require('gulp');

// CSS
const sass = require('gulp-sass')(require('sass'));
const plumber = require('gulp-plumber');
const autoprefixer = require('autoprefixer');
const cssnano = require('cssnano');
const postcss = require('gulp-postcss');
const sourcemaps = require('gulp-sourcemaps');

// Imágenes
const cache = require('gulp-cache');
const imagemin = require('gulp-imagemin');
const webp = require('gulp-webp');
const svg = require('gulp-svgmin');//svg
// Paquete comentado para que no falle en el linux
// de los ordenadores pequeños
const avif = require('gulp-avif');

// JavaScript
const concat = require('gulp-concat');
const terser = require('gulp-terser-js');
const rename = require('gulp-rename');
const allowedExtensions = 'png,PNG,jpg,JPG,jpeg,JPEG,gif,GIF';
const lowerCaseExtensions = 'png,jpg,jpeg,gif';

function css( done ) {
	// Identificar el archivo css a compilar
	src('src/scss/**/*.scss')
		.pipe( sourcemaps.init() )
		.pipe( plumber() )
		.pipe( sass() )  // Compilarlo
		// Desactivados en desarrollo. Cuando se termine la aplicación se desactivan las dos para minificar
		//.pipe( postcss([autoprefixer(), cssnano()]) )
		.pipe( sourcemaps.write('.') )
		.pipe( dest('./public/build/css') );  // Almacenarla en el disco duro
	done();
}

function imagenes( done ) {
	const opciones = {
		optimizationLevel: 3
	};
	src('src/images/**/*.{'+allowedExtensions+'}')
		.pipe( rename(path => {
			path.dirname = path.dirname.toLowerCase();
			path.basename = path.basename.toLowerCase();
       		path.extname = path.extname.toLowerCase();
		}))
		.pipe( cache( imagemin(opciones) ) )
		.pipe( dest('public/build/images'));

	done();
}

function versionWebp( done ) {
	const opciones = {
		quality: 50
	};
	src('src/images/**/*.{'+allowedExtensions+'}')
		.pipe( webp(opciones) )
		.pipe( dest('public/build/images') );

	done();
}

function versionAvif( done ) {
	const opciones = {
		quality: 50
	};
	src('src/images/**/*.{'+allowedExtensions+'}')
		.pipe( avif(opciones) )
		.pipe( dest('public/build/images') );

	done();
}

function versionSVG( done ){
    src('src/images/**/*.svg')
        // .pipe( svg() )
        .pipe( dest('public/build/images') );
    done();
}

function javascript( done ) {
    src('src/js/**/*.js')
		.pipe( sourcemaps.init() )
        .pipe(concat('bundle.js')) // final output file name
		.pipe( terser() )
        .pipe(rename({ suffix: '.min' }))
		.pipe( sourcemaps.write('.'))
        .pipe( dest('public/build/js') );
    done();
}

function dev( done ) {
	watch('src/scss/**/*.scss', css);
    // watch('src/images/**/*.{'+allowedExtensions+'}', versionWebp);
    // watch('src/images/**/*.{'+allowedExtensions+'}', versionAvif);
    // watch('src/images/**/*.{'+allowedExtensions+'}', versionSVG);
    // watch('src/images/**/*.{'+allowedExtensions+'}', imagenes);
    done();
}

exports.css = css;
exports.js = javascript;
exports.imagenes = imagenes;
exports.versionWebp = versionWebp;
exports.versionAvif = versionAvif;
exports.versionSVG = versionSVG;
exports.minImages = parallel(imagenes, versionWebp, versionAvif, versionSVG);
// exports.dev = parallel( javascript, dev );
exports.dev = dev;
exports.default = dev;