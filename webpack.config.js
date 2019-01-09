const path = require('path');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const webpack = require('webpack');

module.exports = {
	entry: {
		'index.js': './assets/index.js',
		'index.css': './assets/index.scss',
	},
	output: {
		filename: '[name]',
		path: path.resolve(__dirname, 'www/assets')
	},
	module: {
		rules: [
			{
				test: /.(scss)$/,
				use: ExtractTextPlugin.extract({
					use: [
						{loader: 'css-loader', options: {minimize: true}},
						{
							loader: 'postcss-loader',
							options: {
								plugins: function () {
									return [require('precss'), require('autoprefixer')];
								}
							}
						},
						{loader: 'resolve-url-loader'},
						{loader: 'sass-loader', options: {sourceMap: true}}
					]
				})
			}, {
				test: /\.(woff|woff2|eot|ttf|otf|svg)$/,
				use: [
					'file-loader'
				]
			}
		]
	},
	plugins: [
		new ExtractTextPlugin('index.css'),
		new webpack.optimize.UglifyJsPlugin(),
	]
};
