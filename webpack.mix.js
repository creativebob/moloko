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

// Ckeditor
// mix.copy('node_modules/ckeditor/config.js', 'public_html/js/plugins/ckeditor/config.js')
// 	.copy('node_modules/ckeditor/styles.js', 'public_html/js/plugins/ckeditor/styles.js')
// 	.copy('node_modules/ckeditor/contents.css', 'public_html/js/plugins/ckeditor/contents.css')
// 	.copyDirectory('node_modules/ckeditor/skins', 'public_html/js/plugins/ckeditor/skins')
// 	.copyDirectory('node_modules/ckeditor/lang', 'public_html/js/plugins/ckeditor/lang')
// 	.copyDirectory('node_modules/ckeditor/plugins', 'public_html/js/plugins/ckeditor/plugins');

mix
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
// .sourceMaps()
;
// mix.browserSync('crmsystem.local/admin/test');

if (mix.inProduction()) {
	mix.version();
} else {
	mix.disableNotifications();
}
