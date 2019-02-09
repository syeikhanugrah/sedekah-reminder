let Encore = require('@symfony/webpack-encore');
const CopyWebpackPlugin = require('copy-webpack-plugin');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .autoProvidejQuery()
    .enableSassLoader()
    .addEntry('js/app', './assets/js/app.js')
    .addStyleEntry('css/app', './assets/scss/app.scss')
    .addStyleEntry('css/login', './assets/scss/login.scss')
    .addStyleEntry('css/registration', './assets/scss/registration.scss')
    .addStyleEntry('css/pengingat/index', './assets/scss/pengingat/index.scss')
    .enableSourceMaps(!Encore.isProduction())
    .enableSingleRuntimeChunk()
    .addPlugin(new CopyWebpackPlugin([
        { from: './assets/images', to: 'images' }
    ]))
;

module.exports = Encore.getWebpackConfig();
