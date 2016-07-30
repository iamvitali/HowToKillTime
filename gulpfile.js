var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    var modulesPath = 'node_modules/';

    mix.sass('app.scss')
        .copy(modulesPath + 'bootstrap-sass/assets/fonts', 'public/fonts')
        .copy(modulesPath + 'bootstrap-sass/assets/javascripts/bootstrap.min.js', 'public/js')
        .copy(modulesPath + 'font-awesome/fonts', 'public/fonts');
});