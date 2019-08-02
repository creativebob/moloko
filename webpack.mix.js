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

mix.copyDirectory('resources/scss/system/fonts', 'public_html/css/system/fonts');
mix.copyDirectory('resources/scss/system/img', 'public_html/css/system/img');
mix.copyDirectory('resources/scss/system/sprites', 'public_html/css/system/sprites');

mix
	// .copy('resources/scss/system/', 'public_html/css/system')
	.sass('resources/scss/system/app.scss', 'public_html/css/system', {

	})
	.options({
		processCssUrls: false,
		// postCss: [
		// 	require('postcss-css-variables')()
		// ]
	})
	// .sourceMaps()
;

// mix.browserSync('crmsystem.local/admin/test');

if (mix.inProduction()) {
	mix.version();
} else {
	mix.disableNotifications();
}
