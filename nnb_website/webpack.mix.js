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

mix.js('resources/js/app.js', 'public/js')
   .js('resources/js/generate_layers.js', 'public/js')
   .js('resources/js/manage_training.js', 'public/js')
   .js('resources/js/update_realtime_data.js', 'public/js')
   .js('resources/js/dataset_uploader.js', 'public/js')
   .js('resources/js/edit_form_control.js', 'public/js')
   .js('resources/js/handle_submit.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .sass('resources/sass/welcome.scss', 'public/css')
   .sass('resources/sass/progress_bar.scss', 'public/css');

   