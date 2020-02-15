const merge = require('webpack-merge');
const common = require('./webpack.common.js');
const glob = require('glob-all');
const path = require('path');
const OptimizeCSSAssetsPlugin = require("optimize-css-assets-webpack-plugin");
const TerserPlugin = require('terser-webpack-plugin');
const PurgecssPlugin = require('purgecss-webpack-plugin');

module.exports = merge(common, {
  mode: "production",
  optimization: {
    namedChunks: true,
    minimizer: [
      new TerserPlugin(),
      new OptimizeCSSAssetsPlugin({})
    ]
  },
  plugins: [
    new PurgecssPlugin({
      paths: glob.sync([
        path.join(__dirname, 'resources/views/**/*.twig'),
        path.join(__dirname, 'resources/assets/js/components/**/*.vue'),
      ]),
      whitelist: [
        'footer-bubble',
        'line-numbers',
        'token',
        'comment',
        'prolog',
        'doctype',
        'cdata',
        'punctuation',
        'namespace',
        'property',
        'tag',
        'boolean',
        'number',
        'constant',
        'tooltip',
        'bs-tooltip-top',
        'bs-popover-top',
        'show',
        'fade'
      ],
      whitelistPatterns: [
        /^logo/,
        /^language/,
        /^popover/,
      ],
      whitelistPatternsChildren: [
        /hire-me$/,
        /dropdown-menu/
      ]
    })
  ]
});
