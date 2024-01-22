/* global global require, process, module */
var path = require( "path" );
var loadGruntConfig = require( "load-grunt-config" );
var timeGrunt = require( "time-grunt" );
global.developmentBuild = true;

/* global global, require, process */
module.exports = function( grunt ) {
	timeGrunt( grunt );

	const pkg = grunt.file.readJSON( "package.json" );
	const pluginVersion = pkg.yoast.pluginVersion;

	// Define project configuration
	var project = {
		pluginVersion: pluginVersion,
		pluginSlug: "comment-hacks",
		pluginMainFile: "comment-hacks.php",
		paths: {
			/**
			 * Gets the config path.
			 *
			 * @returns {string} The config path.
			 */
			get config() {
				return this.grunt + "config/";
			},
			css: "admin/assets/css/",
			js: "admin/assets/js/",
			grunt: "grunt/",
			assets: "svn-assets/",
			languages: "languages/",
			svnCheckoutDir: ".wordpress-svn",
			vendor: "vendor/",
			logs: "logs/",
		},
		files: {
			css: [
				"admin/assets/css/src/*.css",
				"!admin/assets/css/dist/*.css",
			],
			js: [
				"admin/assets/js/*.js",
				"!admin/assets/js/*.min.js",
			],
			php: [
				"*.php",
				"admin/**/*.php",
				"inc/**/*.php",
			],
			/**
			 * Gets the config path.
			 *
			 * @returns {string} The config path.
			 */
			get config() {
				return project.paths.config + "*.js";
			},
			grunt: "Gruntfile.js",
			artifact: "artifact",
			artifactComposer: "artifact-composer",
		},
		pkg: grunt.file.readJSON( "package.json" ),
	};

	// Used to switch between development and release builds
	if ( [ "release", "artifact", "deploy:trunk", "deploy:master" ].includes( process.argv[ 2 ] ) ) {
		global.developmentBuild = false;
	}

	// Load Grunt configurations and tasks
	loadGruntConfig( grunt, {
		configPath: path.join( process.cwd(), "node_modules/@yoast/grunt-plugin-tasks/config/" ),
		overridePath: path.join( process.cwd(), project.paths.config ),
		data: project,
		jitGrunt: {
			staticMappings: {
				addtextdomain: "grunt-wp-i18n",
				makepot: "grunt-wp-i18n",
				// eslint-disable-next-line camelcase
				glotpress_download: "grunt-glotpress",
				"update-version": "@yoast/grunt-plugin-tasks",
				"set-version": "@yoast/grunt-plugin-tasks",
			},
		},
	} );
};
