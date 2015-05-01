// https://github.com/SaschaGalley/grunt-phpcs
module.exports = {
	options: {
		ignoreExitCode: true
	},
	plugin: {
		options: {
			bin: 'vendor/bin/phpcs',
			extensions: 'php',
			verbose: false
		},
		dir: [
			'<%= files.php %>'
		]
	}
};
