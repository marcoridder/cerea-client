const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

let publicPath = 'public';

let sassOptions = {
    autoprefixer: {
        options: {
            browsers: [
                'last 2 versions', 'IE 11', 'IE 10'
            ]
        }
    }
};

mix.scripts([
    'resources/js/jquery-3.5.1.min.js',
    'vendor/twbs/bootstrap/dist/js/bootstrap.js',
    'resources/js/bs-custom-file-input.js',
    'resources/js/app.js',
], publicPath + '/assets/js/app.js')
    .sass('resources/sass/app.scss', publicPath + '/assets/css').options(sassOptions)
    .copyDirectory('resources/fonts', publicPath + '/assets/fonts')
    .copyDirectory('vendor/fortawesome/font-awesome/webfonts', publicPath + '/assets/webfonts')
    .version()
;
