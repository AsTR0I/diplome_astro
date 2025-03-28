const mix = require('laravel-mix');
const path = require('path');

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .vue({ version: 2 })  // Указываем версию Vue 2
    
// Дополнительно: конфигурация для Vuetify
mix.webpackConfig({
    resolve: {
        alias: {
          '@': path.resolve(__dirname, 'resources/js'),
        },
        extensions: ['.js', '.vue', '.json'],
    },
    output: {
        chunkFilename: () => {
          return mix.inProduction()
            ? 'js/[name]-[contenthash].js'
            : 'js/[name].js';
        },
      },
});

if (mix.inProduction()) {
    mix.version();
}

mix.disableNotifications();
  