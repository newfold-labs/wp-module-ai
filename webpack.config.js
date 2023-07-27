const path = require('path');
const { merge } = require('webpack-merge');
const wpScriptsConfig = require('@wordpress/scripts/config/webpack.config');

const wpUtilWebpackConfig = {
  entry: './src/description.js',
  output: {
    filename: 'index.js',
    path: path.resolve(process.cwd(), `dist`),
  },
  module: {
    rules: [
      {
        test: /\.scss$/,
        use: [
          'style-loader',
          'css-loader',
          'sass-loader'
        ]
      },
      {
        test: /\.css$/,
        use: [
          'style-loader',
          'css-loader'
        ]
      },
    ]
  },
  devtool: 'eval-source-map',
  resolve :{
    alias: {
      react: path.resolve('./node_modules/react'),
      'react-dom': path.resolve('./node_modules/react-dom')
    }
  }
};

module.exports = merge(wpScriptsConfig, wpUtilWebpackConfig);
  