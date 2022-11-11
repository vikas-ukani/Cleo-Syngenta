const mix = require('laravel-mix');

require('mix-tailwindcss');

mix.js('resources/js/app.js', 'public/js')
    .copyDirectory('resources/img', 'public/img')
    .sass('resources/sass/app.scss', 'public/css')
    .tailwind('./tailwind.js');
