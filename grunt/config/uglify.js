// https://github.com/gruntjs/grunt-contrib-uglify
module.exports = {
	'comment-hacks': {
		options: {
			preserveComments: 'some',
			report: 'gzip'
		},
		files: [{
			expand: true,
			cwd: '<%= paths.js %>',
			src: [
				'*.js',
				'!*.min.js'
			],
			dest: '<%= paths.js %>',
			ext: '.min.js',
			extDot: 'first',
			isFile: true
		}]
	}
};
