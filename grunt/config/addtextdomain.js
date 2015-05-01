// https://github.com/blazersix/grunt-wp-i18n
/*jslint node:true */
module.exports = {
	options: {
		textdomain: '<%= pkg.plugin.textdomain %>'
	},
	plugin: {
		files: {
			src: [
				'<%= files.php %>'
			]
		}
	}
};
