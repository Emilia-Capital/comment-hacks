// See https://github.com/sindresorhus/grunt-eslint
module.exports = function( grunt ) {
    const fix = grunt.option( "fix" ) || false;

    return {
        target: {
            src: [ "<%= files.js %>" ],
            options: {
                fix: fix,
            },
        },
    };
};
