var Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

const webpack = require('webpack');

const CopyWebpackPlugin = require('copy-webpack-plugin');

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    .autoProvidejQuery()
    .addPlugin(new CopyWebpackPlugin([
        // copies to {output}/static
        {from: './assets/static', to: 'static'}
    ]))
    // o summmernote tem esta dependência, mas não é necessária
    .addPlugin(new webpack.IgnorePlugin(/^codemirror$/))
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('dashboard_chartVendasTotalPorFornecedor', './assets/js/dashboard_chartVendasTotalPorFornecedor.js')
    .addEntry('dashboard_chartVendasTotalPorVendedor', './assets/js/dashboard_chartVendasTotalPorVendedor.js')
    .addEntry('dashboard_chartEstoque', './assets/js/dashboard_chartEstoque.js')
    .addEntry('dashboard_chartCompras', './assets/js/dashboard_chartCompras.js')
    .addEntry('dashboard_chartContasPagarReceber', './assets/js/dashboard_chartContasPagarReceber.js')

    .addEntry('utils/relatorioPushList', './assets/js/utils/relatorioPushList.js')

    .addEntry('Relatorios/relCtsPagRec01_list', './assets/js/Relatorios/relCtsPagRec01_list.js')
    .addEntry('Relatorios/relVendas01_totaisVendasPorFornecedor', './assets/js/Relatorios/relVendas01_totaisVendasPorFornecedor.js')
    .addEntry('Relatorios/itensVendidosPorFornecedor', './assets/js/Relatorios/itensVendidosPorFornecedor.js')
    .addEntry('Relatorios/preVendasPorVendedor', './assets/js/Relatorios/preVendasPorVendedor.js')
    .addEntry('Relatorios/preVendasPorProduto', './assets/js/Relatorios/preVendasPorProduto.js')
    .addEntry('Relatorios/itensDoPreVenda', './assets/js/Relatorios/itensDoPreVenda.js')
    .addEntry('Relatorios/relCompFor01_listItensCompradosPorFornecedor', './assets/js/Relatorios/relCompFor01_listItensCompradosPorFornecedor.js')
    .addEntry('Relatorios/relCompFor01_list', './assets/js/Relatorios/relCompFor01_list.js')

    .addEntry('Relatorios/relEstoque01_list', './assets/js/Relatorios/relEstoque01_list.js')

    .addEntry('Relatorios/relCompras01_listComprasPorProduto', './assets/js/Relatorios/relCompras01_listComprasPorProduto.js')

    .addEntry('Relatorios/relCliente01_list', './assets/js/Relatorios/relCliente01_list.js')

    .addEntry('Vendas/produto_list', './assets/js/Vendas/produto_list.js')

    .addEntry('Estoque/produto_list', './assets/js/Estoque/produto_list.js')
    .addEntry('Estoque/pedidoCompra_form', './assets/js/Estoque/pedidoCompra_form.js')
    .addEntry('Estoque/pedidoCompraItem_form', './assets/js/Estoque/pedidoCompraItem_form.js')
    .addEntry('Estoque/pedidoCompra_list', './assets/js/Estoque/pedidoCompra_list.js')
    .addEntry('Estoque/pedidoCompra_listReposicao', './assets/js/Estoque/pedidoCompra_listReposicao.js')


    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    // .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    // .enableSingleRuntimeChunk()
    .disableSingleRuntimeChunk()

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

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

// enables Sass/SCSS support
//.enableSassLoader()

// uncomment if you use TypeScript
//.enableTypeScriptLoader()

// uncomment to get integrity="..." attributes on your script & link tags
// requires WebpackEncoreBundle 1.4 or higher
//.enableIntegrityHashes(Encore.isProduction())

// uncomment if you're having problems with a jQuery plugin
//.autoProvidejQuery()

// uncomment if you use API Platform Admin (composer req api-admin)
//.enableReactPreset()
//.addEntry('admin', './assets/js/admin.js')
;

module.exports = Encore.getWebpackConfig();
