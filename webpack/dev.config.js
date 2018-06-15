require('dotenv').config()
const path = require('path')
const webpack = require('webpack')

const appLocale = (process.env.APP_LOCALE=='en' || !process.env.APP_LOCALE) ? '':process.env.APP_LOCALE

const sourcePath = path.resolve(__dirname, '../resources/assets')
const sourceJsPath = path.resolve(__dirname, '../resources/assets/js')
const sourceScssPath = path.resolve(__dirname, '../resources/assets/scss')
const sourceAssetGlobalPath = path.resolve(__dirname, '../resources/assets/global')
const sourceAssetLocalizationPath = path.resolve(__dirname, '../resources/assets/localization')
const targetPath = path.resolve(__dirname, '../public')

const targetImgPath = path.resolve(__dirname, '../public/images')

const ExtractTextPlugin = require('extract-text-webpack-plugin')
const extractSass = new ExtractTextPlugin({
  filename: "css/[name].css",
  allChunks: true
})


const CopyWebpackPlugin = require('copy-webpack-plugin')
const langPattern = appLocale.length ? appLocale : 'en'
const copyStaticAssets = new CopyWebpackPlugin([
  { from: `${sourceAssetGlobalPath}/**/*`, to: targetPath, context: sourceAssetGlobalPath, toType: 'dir'},
  { from: `${sourceAssetLocalizationPath}/${langPattern}/*/**`, to: targetPath, context: `${sourceAssetLocalizationPath}/${langPattern}`, toType: 'dir', force: true},
],
{
  ignore: [
    '.*'
  ]
})


var baseConfig = {
    context: sourcePath,

    entry: {
      comment: './js/comment/index.jsx',
      feedback: './js/feedback/index.jsx',
      management: './js/management/index.jsx',
      admin: './js/admin/index.jsx'
    },

    output: {
      path: targetPath,
      publicPath: targetPath,
      filename: 'js/[name].js'
    },

    resolve: {
      extensions: ['.js', '.jsx', '.css', '.scss']
    },

    module: {
        rules: [
          {
            test: /\.(js|jsx)$/,
            exclude: /node_modules/,
            include: sourceJsPath,
            use: {
              loader: 'babel-loader',
              options: {
                presets: [['es2015', { modules: false }], 'react', 'stage-2']
              }
            }
          },
          {
            test: /\.(scss)$/,
            exclude: /node_modules/,
            include: sourceScssPath,
            use: extractSass.extract({
              fallback: 'style-loader',
              use: [
                {
                  loader: 'css-loader'
                },
                {
                  loader: 'postcss-loader',
                  options: {
                    plugins: function () {
                      return [
                        require('precss'),
                        require('autoprefixer')
                      ]
                    }
                  }
                },
                {
                  loader: 'sass-loader',
                  options: {
                    sourceMap: false,

                  }
                },

              ]
            })
          },
          {
            test: /\.css$/,
            use: [
                {
                  loader: 'style-loader'
                },
                {
                  loader: 'css-loader'
                }
            ]
          },
          { test: /\.(png|woff|woff2|eot|ttf|svg|gif)$/,
            use: [
                {
                    loader: 'url-loader',
                    options: {
                      limit: 100000
                    }
                }
            ]
          }
        ]},
    plugins: [
      extractSass,
      copyStaticAssets,
      new webpack.DefinePlugin({
        'process.env.NODE_ENV': JSON.stringify(process.env.NODE_ENV)
      }),
      new webpack.optimize.CommonsChunkPlugin({
        name: 'vendor'
      })
    ],

    stats: {
      hash: true,
      version: false,
      timings: true,
      children: false,
      errorDetails: true,
      chunks: false,
      modules: true,
      reasons: true,
      source: true,
      publicPath: false
    },

    performance: {
      hints: false
    }
}

module.exports = baseConfig
