/*global require, process, module */
module.exports = function(grunt) {
	'use strict';

	require('time-grunt')(grunt);

	// Define project configuration
	var project = {
		paths: {
			get config() {
				return this.grunt + 'config/';
			},
            css: 'admin/assets/css/',
            js: 'admin/assets/js/',
			grunt: 'grunt/',
			images: 'assets/',
			languages: 'languages/',
			logs: 'logs/'
		},
		files: {
            css: [
                'admin/assets/css/*.css',
                '!admin/assets/css/*.min.css'
            ],
            js: [
                'admin/assets/js/*.js',
                '!admin/assets/js/*.min.js'
            ],
			php: [
				'*.php',
				'admin/**/*.php',
				'frontend/**/*.php',
				'inc/**/*.php'
			],
			phptests: 'tests/**/*.php',
			get config() {
				return project.paths.config + '*.js';
			},
			grunt: 'Gruntfile.js'
		},
		pkg: grunt.file.readJSON( 'package.json' )
	};

	// Load Grunt configurations and tasks
	require( 'load-grunt-config' )(grunt, {
		configPath: require( 'path' ).join( process.cwd(), project.paths.config ),
		data: project,
		jitGrunt: {
			staticMappings: {
				addtextdomain: 'grunt-wp-i18n',
				makepot: 'grunt-wp-i18n',
				glotpress_download: 'grunt-glotpress',
				wpcss: 'grunt-wp-css'
			}
		}
	});
};
