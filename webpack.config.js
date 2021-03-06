var Encore = require('@symfony/webpack-encore');
const WorkboxPlugin = require('workbox-webpack-plugin');
const OptimizeCssAssetsPlugin = require('optimize-css-assets-webpack-plugin');

const TerserPlugin = require('terser-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const PurgeCssPlugin = require('purgecss-webpack-plugin');
const WebpackBar = require('webpackbar');
const glob = require('glob-all');
const path = require('path');
// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {

    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}


Encore
    .addPlugin(new WebpackBar())
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .setManifestKeyPrefix('build/')
    .addEntry('eventSource', './assets/js/eventSource.js')
    .addEntry('app', './assets/js/app.js')
    .addEntry('ajax', './assets/js/ajax.js')
    .addEntry('login', './assets/js/login.js')
    .addEntry('search', './assets/js/search.js')

    .addStyleEntry('main', './assets/css/scss/imports.scss')
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureBabel(() => {
    }, {
        useBuiltIns: 'usage',
        corejs: 3
    })
    .enablePostCssLoader()
    .addLoader({
        test: /\.scss$/,
        use: [
            {
                loader: MiniCssExtractPlugin.loader,
                options: {
                    // you can specify a publicPath here
                    // by default it uses publicPath in webpackOptions.output
                    hmr: process.env.NODE_ENV === 'production',
                },
            },
            'css-loader',
            'sass-loader',
        ],
    })
    .addPlugin(
        new MiniCssExtractPlugin({
            filename: Encore.isProduction() ? '[name].[contenthash].css' : '[name].css',
        }),
    )

    .addPlugin(
        new OptimizeCssAssetsPlugin({
            assetNameRegExp: /\.(c|s[ac])ss$/,
            cssProcessorPluginOptions: {
                preset: [
                    'default',
                    {
                        discardComments: {
                            removeAll: true, // remove any comments?
                        },
                    },
                ],
            },
            canPrint: true,
        }),
    )



    // .addPlugin(
    //     new PurgeCssPlugin({
    //         // folders: ['resources/views/**/*', 'resources/assets/scss/'],
    //         paths: glob.sync([path.join(__dirname, 'templates/**/*.html.twig')]),
    //         whitelistPatterns: [],
    //     }),
    // )


    .addPlugin(
        new WorkboxPlugin.GenerateSW({
            // these options encourage the ServiceWorkers to get in there fast
            // and not allow any straggling "old" SWs to hang around
            swDest: './../service-worker.js',
            clientsClaim: true,
            skipWaiting: true,


           // runtimeCaching: [{
                //urlPattern: new RegExp(/^(?!https:\/\/s-website\.ga\/\.well-known\/mercure\?topic=%2Fmessage).*$/),
             //   urlPattern: '/',
                //urlPattern: new RegExp(/^(http:\/\/localhost:8000\.*$)\.*/),
              //  handler: 'StaleWhileRevalidate',
            //}]
        }))
    .enableVueLoader()
;
const prod = Encore.getWebpackConfig();
prod.name = 'prod';

Encore.reset();

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .setManifestKeyPrefix('build/')
    .addEntry('eventSource', './assets/js/eventSource.js')
    .addEntry('app', './assets/js/app.js')

    .addEntry('ajax', './assets/js/ajax.js')
    .addEntry('search', './assets/js/search.js')
    .addEntry('login', './assets/js/login.js')
    .addStyleEntry('main', './assets/css/scss/imports.scss')
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureBabel(() => {
    }, {
        useBuiltIns: 'usage',
        corejs: 3
    })
    .enableSassLoader()
    .enableVueLoader()
;
const dev = Encore.getWebpackConfig();
dev.name = 'dev';


module.exports = [prod, dev];





