/*
 * For a detailed explanation regarding each configuration property, visit:
 * https://jestjs.io/docs/configuration
 */
/*
 * For a detailed explanation regarding each configuration property, visit:
 * https://jestjs.io/docs/configuration
 */

module.exports = {
	testEnvironment: 'jest-environment-jsdom',
	setupFilesAfterEnv: ['<rootDir>/jest.setup.js'],
	moduleFileExtensions: [
		'js',
		'json',
		'vue',
	],
	transform: {
		// process `*.vue` files with `vue-jest`
		'.*\\.(js)$': 'babel-jest',
		'.*\\.(vue)$': 'vue-jest',
	},
}
