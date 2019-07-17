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

mix.setPublicPath('public_html');

mix.js('resources/js/system/app.js', 'public_html/js/system');
// mix.sass('resources/scss/system/app.scss', 'public_html/css/system');

// mix.browserSync('crmsystem.local/admin/test');

if (mix.inProduction()) {
	mix.version();
}
