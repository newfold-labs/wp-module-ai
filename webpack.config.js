const path = require('path');
const { merge } = require('webpack-merge');
const wpScriptsConfig = require('@wordpress/scripts/config/webpack.config');

const wpUtilWebpackConfig = {
  entry: './src/description.js',
  output: {
    filename: 'index.js',
    path: path.resolve(process.cwd(), `dist`),
  }
};

module.exports = merge(wpScriptsConfig, wpUtilWebpackConfig);
  