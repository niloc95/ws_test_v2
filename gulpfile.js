/* ----------------------------------------------------------------------------
 * @webSchedulr - Online Appointment Scheduler
 *
 * @package     @webSchedulr
 * @author      N. Cara <nilo.cara@frontend.co.za>
 * @copyright   Copyright (c) Nilo Cara
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://webschedulr.co.za
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */

const babel = require('gulp-babel');
const changed = require('gulp-changed');
const cached = require('gulp-cached');
const childProcess = require('child_process');
const css = require('gulp-clean-css');
const del = require('del');
const fs = require('fs-extra');
const gulp = require('gulp');
const plumber = require('gulp-plumber');
const rename = require('gulp-rename');
const sass = require('gulp-sass')(require('sass'));
const zip = require('zip-dir');

function archive(done) {
    const filename = '@webSchedulr-0.0.0.zip';

    // Check and remove 'build' directory if it exists
    if (fs.existsSync('build')) {
        fs.removeSync('build');
        console.log('Removed existing build directory.');
    }

    // Check and remove build zip if it exists
    if (fs.existsSync(filename)) {
        fs.removeSync(filename);
        console.log('Removed existing build zip.');
    }

    fs.mkdirsSync('build');
    fs.copySync('application', 'build/application');
    fs.copySync('assets', 'build/assets');
    fs.copySync('system', 'build/system');

    fs.ensureDirSync('build/storage/backups');
    fs.copySync('storage/backups/.htaccess', 'build/storage/backups/.htaccess');
    fs.copySync('storage/backups/index.html', 'build/storage/backups/index.html');

    fs.ensureDirSync('build/storage/cache');
    fs.copySync('storage/cache/index.html', 'build/storage/cache/index.html');
    fs.copySync('storage/cache/.htaccess', 'build/storage/cache/.htaccess');

    fs.ensureDirSync('build/storage/logs');
    fs.copySync('storage/logs/.htaccess', 'build/storage/logs/.htaccess');
    fs.copySync('storage/logs/index.html', 'build/storage/logs/index.html');

    fs.ensureDirSync('build/storage/sessions');
    fs.copySync('storage/sessions/.htaccess', 'build/storage/sessions/.htaccess');
    fs.copySync('storage/sessions/index.html', 'build/storage/sessions/index.html');

    fs.ensureDirSync('build/storage/uploads');
    fs.copySync('storage/uploads/index.html', 'build/storage/uploads/index.html');

    fs.copySync('index.php', 'build/index.php');
    fs.copySync('patch.php', 'build/patch.php');
    fs.copySync('composer.json', 'build/composer.json');
    fs.copySync('composer.lock', 'build/composer.lock');
    fs.copySync('.env.example', 'build/.env.example');
    fs.copySync('CHANGELOG.md', 'build/CHANGELOG.md');
    fs.copySync('README.md', 'build/README.md');
    fs.copySync('LICENSE', 'build/LICENSE');
    fs.copySync('setup.php', 'build/setup.php');
    fs.copySync('.htaccess', 'build/.htaccess');

    childProcess.execSync('cd build && composer install --no-interaction --no-dev --no-scripts --optimize-autoloader');

    fs.removeSync('build/composer.lock');
    del.sync('**/.DS_Store');
    del.sync('build/**/.git');

    zip('build', {saveTo: filename}, function (error) {
        if (error) {
            console.log('Zip Error', error);
        }

        done();
    });
}

function clean(done) {
    // Delete all CSS and minified CSS files
    fs.removeSync('assets/css/**/*.css');
    fs.removeSync('assets/css/**/*.min.css');
    done();
}

function scripts() {
    return gulp
        .src(['assets/js/**/*.js', '!assets/js/**/*.min.js'])
        .pipe(plumber())
        .pipe(changed('assets/js/**/*'))
        .pipe(babel({comments: false}))
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('assets/js'));
}

function styles() {
    return gulp
        .src(['assets/css/**/*.scss', '!assets/css/**/*.min.css'])
        .pipe(plumber())
        .pipe(cached())
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest('assets/css'))
        .pipe(css())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('assets/css'));
}

function watch(done) {
    gulp.watch(['assets/js/**/*.js', '!assets/js/**/*.min.js'], gulp.parallel(scripts));
    gulp.watch(['assets/css/**/*.scss', '!assets/css/**/*.css'], gulp.parallel(styles));
    done();
}

function vendor(done) {
    del.sync(['assets/vendor/**', '!assets/vendor/index.html']);

    // bootstrap
    gulp.src([
        'node_modules/bootstrap/dist/js/bootstrap.min.js',
        'node_modules/bootstrap/dist/css/bootstrap.min.css',
    ]).pipe(gulp.dest('assets/vendor/bootstrap'));

    // @fortawesome-fontawesome-free
    gulp.src([
        'node_modules/@fortawesome/fontawesome-free/js/fontawesome.min.js',
        'node_modules/@fortawesome/fontawesome-free/js/solid.min.js',
    ]).pipe(gulp.dest('assets/vendor/@fortawesome-fontawesome-free'));

    // cookieconsent
    gulp.src([
        'node_modules/cookieconsent/build/cookieconsent.min.js',
        'node_modules/cookieconsent/build/cookieconsent.min.css',
    ]).pipe(gulp.dest('assets/vendor/cookieconsent'));

    // fullcalendar
    gulp.src(['node_modules/fullcalendar/index.global.min.js']).pipe(gulp.dest('assets/vendor/fullcalendar'));

    // fullcalendar-moment
    gulp.src(['node_modules/@fullcalendar/moment/index.global.min.js']).pipe(
        gulp.dest('assets/vendor/fullcalendar-moment'),
    );

    // jquery
    gulp.src(['node_modules/jquery/dist/jquery.min.js']).pipe(gulp.dest('assets/vendor/jquery'));

    // jquery-jeditable
    gulp.src(['node_modules/jquery-jeditable/dist/jquery.jeditable.min.js']).pipe(
        gulp.dest('assets/vendor/jquery-jeditable'),
    );

    // moment
    gulp.src(['node_modules/moment/min/moment.min.js']).pipe(gulp.dest('assets/vendor/moment'));

    // moment-timezone
    gulp.src(['node_modules/moment-timezone/builds/moment-timezone-with-data.min.js']).pipe(
        gulp.dest('assets/vendor/moment-timezone'),
    );

    // @popperjs-core
    gulp.src(['node_modules/@popperjs/core/dist/umd/popper.min.js']).pipe(gulp.dest('assets/vendor/@popperjs-core'));

    // select2
    gulp.src(['node_modules/select2/dist/js/select2.min.js', 'node_modules/select2/dist/css/select2.min.css']).pipe(
        gulp.dest('assets/vendor/select2'),
    );

    // tippy.js
    gulp.src(['node_modules/tippy.js/dist/tippy-bundle.umd.min.js']).pipe(gulp.dest('assets/vendor/tippy.js'));

    // trumbowyg
    gulp.src(['node_modules/trumbowyg/dist/trumbowyg.min.js', 'node_modules/trumbowyg/dist/ui/trumbowyg.min.css']).pipe(
        gulp.dest('assets/vendor/trumbowyg'),
    );

    gulp.src(['node_modules/trumbowyg/dist/ui/icons.svg']).pipe(gulp.dest('assets/vendor/trumbowyg/ui'));

    // flatpickr
    gulp.src(['node_modules/flatpickr/dist/flatpickr.min.js', 'node_modules/flatpickr/dist/flatpickr.min.css']).pipe(
        gulp.dest('assets/vendor/flatpickr'),
    );

    gulp.src(['node_modules/flatpickr/dist/themes/material_green.css'])
        .pipe(css())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('assets/vendor/flatpickr'));

    done();
}

// function copyEnvTemplate(done) {
//     // Copy .env.example to .env in the build directory if .env does not already exist
//     const src = '.env.example';
//     const dest = 'build/.env';
//     if (!fs.existsSync(dest)) {
//         fs.copySync(src, dest);
//         console.log('.env file created in build/ from .env.example');
//     } else {
//         console.log('.env already exists in build/, not overwritten.');
//     }
//     done();
// }

exports.clean = gulp.series(clean);
exports.vendor = gulp.series(vendor);
exports.scripts = gulp.series(scripts);
exports.styles = gulp.series(styles);
exports.compile = gulp.series(clean, vendor, scripts, styles);
exports.dev = gulp.series(clean, vendor, scripts, styles, watch);
exports.build = gulp.series(clean, vendor, scripts, styles, archive);
exports.default = exports.dev;
