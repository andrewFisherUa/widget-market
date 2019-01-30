
var gulp = require('gulp');
var minify = require('gulp-minify');
var uglify = require('gulp-uglify');
var browserify = require('gulp-browserify');
gulp.task('js', function() {
    gulp.src('js_projects/src/*.js')
   .pipe(browserify())
   .pipe(minify())
   .pipe(gulp.dest('public/projects'));
 });
gulp.task('css', function(){
   gulp.src('js_projects/styles/*.css')
   .pipe(minify())
   .pipe(gulp.dest('public/projects/styles/'));
});
gulp.task('default',['js','css'],function(){
});