'use strict';

var autoprefixer = require('gulp-autoprefixer');
var csso = require('gulp-csso');
var gulp = require('gulp');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var sass = require('gulp-sass')(require('sass'));

const AUTOPREFIXER_BROWSERS = [
	'ie >= 10',
	'ie_mob >= 10',
	'ff >= 30',
	'chrome >= 34',
	'safari >= 7',
	'opera >= 23',
	'ios >= 7',
	'android >= 4.4',
	'bb >= 10'
];

gulp.task('styles', function () {
	return gulp.src('./scss/index.scss')
		.pipe(sass().on('error', sass.logError))
		.pipe(autoprefixer({browsers: AUTOPREFIXER_BROWSERS}))
		.pipe(csso())
		.pipe(rename('index.min.css'))
		.pipe(gulp.dest('./public/css'));
});

gulp.task('default', gulp.parallel('styles'));

gulp.task('watch', function() {
	gulp.watch([
		'./scss/**/*.scss'
	], gulp.series('styles')); 
})