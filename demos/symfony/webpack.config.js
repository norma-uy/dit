const Encore = require('@symfony/webpack-encore')
const WebpackRTLPlugin = require('@automattic/webpack-rtl-plugin')

/// ///////////// WEBSITE CONFIG //////////////////

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev')
}

Encore

    // directory where compiled assets will be stored
    .setOutputPath('public/build/website/')

    // public path used by the web server to access the output path
    .setPublicPath('/build/website/')

    // only needed for CDN's or subdirectory deploy
    // .setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/website/index.js')
    .addEntry('app-home', './assets/website/home.js')
    .addEntry('app-contact', './assets/website/contact.js')
    .addEntry('app-post', './assets/website/post.js')
    .addEntry('app-recaptcha', './assets/website/recaptcha.js')
    .addStyleEntry('global', './assets/website/styles/index.scss')
    .addStyleEntry('global-mobile', './assets/website/styles/mobile.scss')

    .copyFiles({
        from: './assets/website/images',

        // optional target path, relative to the output dir
        to: 'images/[path][name].[ext]'

        // if versioning is enabled, add the file hash too
        // to: 'images/[path][name].[hash:8].[ext]',

        // only copy files matching this pattern
        // pattern: /\.(png|jpg|jpeg)$/
    })

    .configureFontRule({
        type: 'asset'

        // maxSize: 4 * 1024
    })

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    // .enableStimulusBridge('./assets/controllers.json')

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

    // configure Babel
    // .configureBabel((config) => {
    //     config.plugins.push('@babel/a-babel-plugin');
    // })

    // enables and configure @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage'
        config.corejs = '3.23'
    })

    // enables Sass/SCSS support
    .enableSassLoader()
    .enablePostCssLoader()

    // uncomment if you use TypeScript
    // .enableTypeScriptLoader()

    // uncomment if you use React
    // .enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    // .enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()

// build the first configuration
const websiteConfig = Encore.getWebpackConfig()

// Set a unique name for the config (needed later!)
websiteConfig.name = 'website-config'

// reset Encore to build the second config
Encore.reset()

/// ////////////////////////////////////////////////////

/// ///////////// ADMIN CONFIG //////////////////

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev')
}

Encore

    // directory where compiled assets will be stored
    .setOutputPath('public/build/admin/')

    // public path used by the web server to access the output path
    .setPublicPath('/build/admin/')

    // only needed for CDN's or subdirectory deploy
    // .setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/admin/app.js')
    .addEntry('form', './assets/admin/form.js')
    .addEntry('page-layout', './assets/admin/page-layout.js')
    .addEntry('page-color-scheme', './assets/admin/page-color-scheme.js')
    .addEntry('field-boolean', './assets/admin/field/field-boolean.js')
    .addEntry('field-code-editor', './assets/admin/field/field-code-editor.js')
    .addEntry('field-collection', './assets/admin/field/field-collection.js')
    .addEntry('field-file-upload', './assets/admin/field/field-file-upload.js')
    .addEntry('field-image', './assets/admin/field/field-image.js')
    .addEntry('field-slug', './assets/admin/field/field-slug.js')
    .addEntry('field-textarea', './assets/admin/field/field-textarea.js')
    .addEntry('field-text-editor', './assets/admin/field/field-text-editor.js')
    .addEntry('mediaField', './assets/admin/field/mediaField.js')
    .addEntry('textEditorField', './assets/admin/field/textEditorField.js')
    .addEntry('rowGalleryField', './assets/admin/field/rowGalleryField.js')
    .addEntry('sliderGalleryField', './assets/admin/field/sliderGalleryField.js')
    .addEntry('thumbnailPhotoField', './assets/admin/field/thumbnailPhotoField.js')
    .addEntry('login', './assets/admin/login.js')

    .copyFiles({
        from: './assets/admin/lib/fontawesome/fonts/',
        // relative to the output dir
        to: 'fonts/[name].[hash].[ext]'
    })

    .copyFiles({
        from: './assets/admin/images/flags',

        // optional target path, relative to the output dir
        to: 'images/flags/[path][name].[ext]'

        // if versioning is enabled, add the file hash too
        // to: 'images/[path][name].[hash:8].[ext]',

        // only copy files matching this pattern
        // pattern: /\.(png|jpg|jpeg)$/
    })

    .copyFiles({
        from: './assets/admin/lib/tinymce/skins',
        to: 'tinymce/skins/[path]/[name].[ext]'
    })

    .copyFiles({
        from: './assets/admin/lib/tinymce/langs',
        to: 'tinymce/langs/[path]/[name].[ext]'
    })
    // .addPlugin(new WebpackRTLPlugin())

    .configureFontRule({
        type: 'asset'

        // maxSize: 4 * 1024
    })

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    // .enableStimulusBridge('./assets/controllers.json')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    // .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
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

    // configure Babel
    // .configureBabel((config) => {
    //     config.plugins.push('@babel/a-babel-plugin');
    // })

    // enables and configure @babel/preset-env polyfills
    // .configureBabelPresetEnv((config) => {
    //     config.useBuiltIns = 'usage'
    //     config.corejs = '3.23'
    // })

    // enables Sass/SCSS support
    .enableSassLoader()
    .enablePostCssLoader()

    // uncomment if you use TypeScript
    // .enableTypeScriptLoader()

    // uncomment if you use React
    // .enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    // .enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()

// build the first configuration
const adminConfig = Encore.getWebpackConfig()

// Set a unique name for the config (needed later!)
adminConfig.name = 'admin-config'

// reset Encore to build the second config
Encore.reset()

/// ////////////////////////////////////////////////////

module.exports = [websiteConfig, adminConfig]
