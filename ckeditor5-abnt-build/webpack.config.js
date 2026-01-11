'use strict';

const path = require('path');
const webpack = require('webpack');
const { bundler, styles } = require('@ckeditor/ckeditor5-dev-utils');
const TerserPlugin = require('terser-webpack-plugin');

module.exports = {
    mode: 'production',
    performance: { hints: false },

    entry: path.resolve(__dirname, 'src', 'ckeditor.js'),

    output: {
        path: path.resolve(__dirname, '../public/assets/ckeditor'),
        filename: 'ckeditor.js',

        library: 'ClassicEditor',
        libraryTarget: 'umd',
        libraryExport: 'default',

        globalObject: 'window'
    },

    optimization: {
        minimizer: [
            new TerserPlugin({
                terserOptions: {
                    ecma: 2018,
                    format: { comments: /^!/ }
                },
                extractComments: false
            })
        ]
    },

    plugins: [
        new webpack.BannerPlugin({
            banner: bundler.getLicenseBanner(),
            raw: true
        })
    ],

    module: {
        rules: [

            // SVG — obrigatório para CKEditor
            {
                test: /\.svg$/,
                use: [ {
                    loader: 'raw-loader'
                } ]
            },

            // CSS oficial do CKEditor
            {
                test: /\.css$/,
                use: [
                    {
                        loader: 'style-loader',
                        options: {
                            injectType: 'singletonStyleTag',
                            attributes: { 'data-cke': true }
                        }
                    },
                    'css-loader',
                    {
                        loader: 'postcss-loader',
                        options: {
                            postcssOptions: styles.getPostCssConfig({
                                themeImporter: {
                                    themePath: require.resolve('@ckeditor/ckeditor5-theme-lark')
                                },
                                minify: true
                            })
                        }
                    }
                ]
            }
        ]
    }
};
