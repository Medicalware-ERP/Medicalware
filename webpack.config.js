const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/js/app.ts')
    .addEntry('datatableGeneric', './assets/js/datatable/datatableGeneric.ts')
    .addEntry('layout.show', './assets/js/layout/layout_show.ts')
    .addEntry('sidebar', './assets/js/sidebar/sidebar.ts')
    .addEntry('form.collection', './assets/js/util/form_collection.ts')

    .addEntry('human_resources.show', './assets/js/human_resources/show.ts')
    .addEntry('human_resources.datatable', './assets/js/human_resources/datatable.ts')
    .addEntry('room.index', './assets/js/room/index.ts')
    .addEntry('room.show', './assets/js/room/show.ts')
    .addEntry('patient.datatable', './assets/js/patient/datatable.ts')
    .addEntry('patient.show', './assets/js/patient/show.ts')
    .addEntry('provider.datatable', './assets/js/provider/datatable.ts')
    .addEntry('provider.show', './assets/js/provider/show.ts')
    .addEntry('invoice.datatable', './assets/js/invoice/datatable.ts')
    .addEntry('invoice.form', './assets/js/invoice/form.ts')
    .addEntry('stock.index', './assets/js/stock/datatable.ts')
    .addEntry('stock.show', './assets/js/stock/show.ts')
    .addEntry('order.datatable', './assets/js/order/datatable.ts')
    .addEntry('order.form', './assets/js/order/form.ts')
    .addEntry('service.index', './assets/js/service/index.ts')


    .addStyleEntry('room.show.style', './assets/styles/room/show.scss')
    .addStyleEntry('room.index.style', './assets/styles/room/index.scss')

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    .enableStimulusBridge('./assets/controllers.json')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

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

    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    // enables Sass/SCSS support
    .enableSassLoader()

    .copyFiles({
        from: './assets/images',

        // optional target path, relative to the output dir
        to: 'images/[path][name].[ext]',

        // if versioning is enabled, add the file hash too
        //to: 'images/[path][name].[hash:8].[ext]',

        // only copy files matching this pattern
        //pattern: /\.(png|jpg|jpeg)$/
    })

    .copyFiles({
        from: './assets/videos',

        // optional target path, relative to the output dir
        to: 'videos/[path][name].[ext]',

        // if versioning is enabled, add the file hash too
        //to: 'images/[path][name].[hash:8].[ext]',

        // only copy files matching this pattern
        //pattern: /\.(png|jpg|jpeg)$/
    })

    // uncomment if you use TypeScript
    .enableTypeScriptLoader()

    // uncomment if you use React
    //.enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    //.autoProvidejQuery()
    ;

module.exports = Encore.getWebpackConfig();
