const path = require('path')
const webpackConfig = require('@nextcloud/webpack-vue-config')

module.exports = {
	...webpackConfig,
	entry: {
		sidebartab: path.resolve(path.join('src', 'sidebartab.js')),
		adminsettings: path.resolve(path.join('src', 'adminsettings.js')),
	},
}
