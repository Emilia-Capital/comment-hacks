// Custom task
/* eslint-disable no-useless-escape */
module.exports = {
	options: {
		version: "<%= pluginVersion %>",
	},
	readme: {
		options: {
			regEx: /(Stable tag:\s+)(\d+(\.\d+){0,3})([^\n^\.\d]?.*?)(\n)/,
			preVersionMatch: "$1",
			postVersionMatch: "$5",
		},
		src: "readme.txt",
	},

	// When changing or adding entries, make sure to update `aliases.yml` for "update-version-trunk".
	pluginFile: {
		options: {
			regEx: /(\* Version:\s+)(\d+(\.\d+){0,3})([^\n^\.\d]?.*?)(\n)/,
			preVersionMatch: "$1",
			postVersionMatch: "$5",
		},
		src: "<%= pluginMainFile %>",
	},
	initializer: {
		options: {
			// Reason for disable: it triggers on a \x0a character we can't find in the string below.
			// eslint-disable-next-line no-control-regex
			regEx: new RegExp( "/(define\( \'COMMENT_HACKS_VERSION\'\, \')(\d+(\.\d+){0.3})([^\.^\'\d]?.*?)(\' \);\n)/" ),
			preVersionMatch: "$1",
			postVersionMatch: "$5",
		},
		src: "<%= pluginMainFile %>",
	},
};
/* eslint-enable no-useless-escape */
