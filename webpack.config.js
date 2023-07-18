const path = require('path');
module.exports = {
    entry: './src/description.js',
    output: {
      filename: 'index.js'
    },
    module: {
      rules: [
        {
          test: /\.(js|jsx)$/,
          exclude: /node_modules/,
          use: {
            loader: 'babel-loader',
            options: {
              presets: ['@babel/preset-react']
            }
          }
        },
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
        react: path.resolve('./node_modules/react')
      }
    }
 
  };
  