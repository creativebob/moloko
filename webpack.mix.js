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

// System
mix.js('resources/js/system/app.js', 'public_html/js/system');

mix.copyDirectory('resources/scss/system/fonts', 'public_html/css/system/fonts');
mix.copyDirectory('resources/scss/system/img', 'public_html/css/system/img');
mix.copyDirectory('resources/scss/system/sprites', 'public_html/css/system/sprites');

mix
	.sass('resources/scss/system/app.scss', 'public_html/css/system', {

	})
	.options({
		processCssUrls: false,
		// postCss: [
		// 	require('postcss-css-variables')()
		// ]
	})
	.minify('public_html/css/system/app.css')
	// .sourceMaps()
;

mix
	.sass('resources/scss/system/partials/print.scss', 'public_html/css/system', {

	})
	.options({
		processCssUrls: false,
		// postCss: [
		// 	require('postcss-css-variables')()
		// ]
	})
	.minify('public_html/css/system/print.css')
	// .sourceMaps()
;

// mix.browserSync('crmsystem.local/admin/test');

// Project
mix.js('resources/js/project/app.js', 'public_html/js/project');

// mix.copyDirectory('resources/scss/project/fonts', 'public_html/css/project/fonts');
// mix.copyDirectory('resources/scss/project/img', 'public_html/css/project/img');
// mix.copyDirectory('resources/scss/project/sprites', 'public_html/css/project/sprites');

mix
	.sass('resources/scss/project/app.scss', 'public_html/css/project', {

	})
	.options({
		processCssUrls: false,
		// postCss: [
		// 	require('postcss-css-variables')()
		// ]
	})
	.minify('public_html/css/project/app.css')
// .sourceMaps()
;
// mix.browserSync('crmsystem.local/admin/test');

if (mix.inProduction()) {
	mix.version();
} else {
	mix.disableNotifications();
}
