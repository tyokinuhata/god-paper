const gulp = require('gulp')
const minifyJs = require('gulp-uglify')
const minifyCss = require('gulp-clean-css')

gulp.task('minifyJs', () => {
    return gulp.src('public/js/app.js')
        .pipe(minifyJs())
        .pipe(gulp.dest('public/js'))
})

gulp.task('minifyCss', () => {
    return gulp.src('public/css/app.css')
        .pipe(minifyCss())
        .pipe(gulp.dest('public/css'))
})

gulp.task('default', [
    'minifyJs',
    'minifyCss'
])