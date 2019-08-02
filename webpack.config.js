var Encore = require('@symfony/webpack-encore');

const webpack = require('webpack');

const CopyWebpackPlugin = require('copy-webpack-plugin');

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    // fixes modules that expect jQuery to be global
    .autoProvidejQuery()
    .addPlugin(new CopyWebpackPlugin([
        // copies to {output}/static
        { from: './assets/static', to: 'static' }
    ]))
    .enableSassLoader()
    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if you JavaScript imports CSS.
     */
    // .createSharedEntry('bse_layout', './assets/js/bse/layout.js')
    .addEntry('dashboard', './assets/js/dashboard.js')
    .addEntry('utils/relatorioPushList', './assets/js/utils/relatorioPushList.js')
    .addEntry('Relatorios/relCtsPagRec01_list', './assets/js/Relatorios/relCtsPagRec01_list.js')
    .addEntry('Relatorios/relVendas01_itensVendidosPorFornecedor', './assets/js/Relatorios/relVendas01_itensVendidosPorFornecedor.js')
    .addEntry('Relatorios/relVendas01_preVendasPorVendedor', './assets/js/Relatorios/relVendas01_preVendasPorVendedor.js')
    .addEntry('Relatorios/relVendas01_itensDoPreVenda', './assets/js/Relatorios/relVendas01_itensDoPreVenda.js')





    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())
    .enableSingleRuntimeChunk()

    // enables Sass/SCSS support
    //.enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you're having problems with a jQuery plugin
    //.autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
