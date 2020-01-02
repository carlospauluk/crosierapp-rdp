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
        {from: './assets/static', to: 'static'}
    ]))
    // o summmernote tem esta dependência, mas não é necessária
    .addPlugin(new webpack.IgnorePlugin(/^codemirror$/))
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
    .addEntry('dashboard_chartVendasTotalPorFornecedor', './assets/js/dashboard_chartVendasTotalPorFornecedor.js')
    .addEntry('dashboard_chartVendasTotalPorVendedor', './assets/js/dashboard_chartVendasTotalPorVendedor.js')
    .addEntry('dashboard_chartEstoque', './assets/js/dashboard_chartEstoque.js')
    .addEntry('dashboard_chartCompras', './assets/js/dashboard_chartCompras.js')
    .addEntry('dashboard_chartContasPagarReceber', './assets/js/dashboard_chartContasPagarReceber.js')

    .addEntry('utils/relatorioPushList', './assets/js/utils/relatorioPushList.js')

    .addEntry('Relatorios/relCtsPagRec01_list', './assets/js/Relatorios/relCtsPagRec01_list.js')
    .addEntry('Relatorios/relVendas01_totaisVendasPorFornecedor', './assets/js/Relatorios/relVendas01_totaisVendasPorFornecedor.js')
    .addEntry('Relatorios/relVendas01_listItensVendidosPorFornecedor', './assets/js/Relatorios/relVendas01_listItensVendidosPorFornecedor.js')
    .addEntry('Relatorios/relVendas01_listPreVendasPorVendedor', './assets/js/Relatorios/relVendas01_listPreVendasPorVendedor.js')
    .addEntry('Relatorios/relVendas01_listPreVendasPorProduto', './assets/js/Relatorios/relVendas01_listPreVendasPorProduto.js')
    .addEntry('Relatorios/relVendas01_listItensDoPreVenda', './assets/js/Relatorios/relVendas01_listItensDoPreVenda.js')
    .addEntry('Relatorios/relCompFor01_listItensCompradosPorFornecedor', './assets/js/Relatorios/relCompFor01_listItensCompradosPorFornecedor.js')
    .addEntry('Relatorios/relCompFor01_list', './assets/js/Relatorios/relCompFor01_list.js')

    .addEntry('Relatorios/relEstoque01_list', './assets/js/Relatorios/relEstoque01_list.js')
    .addEntry('Relatorios/relEstoque01_list_reposicao', './assets/js/Relatorios/relEstoque01_list_reposicao.js')

    .addEntry('Relatorios/relCompras01_listComprasPorProduto', './assets/js/Relatorios/relCompras01_listComprasPorProduto.js')

    .addEntry('Relatorios/relCliente01_list', './assets/js/Relatorios/relCliente01_list.js')

    .addEntry('Vendas/pv_list', './assets/js/Vendas/pv_list.js')
    .addEntry('Vendas/pv_form', './assets/js/Vendas/pv_form.js')
    .addEntry('Vendas/produto_list', './assets/js/Vendas/produto_list.js')


    .addEntry('Estoque/produto_list', './assets/js/Estoque/produto_list.js')





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
    .configureBabel(() => {
    }, {
        useBuiltIns: 'usage',
        corejs: 3
    })
    .enableSingleRuntimeChunk()
// enables Sass/SCSS support
//.enableSassLoader()

// uncomment if you use TypeScript
//.enableTypeScriptLoader()

// uncomment if you're having problems with a jQuery plugin
//.autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
