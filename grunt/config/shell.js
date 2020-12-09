// See https://github.com/sindresorhus/grunt-shell
module.exports = function() {
	return {
		"composer-install-production": {
			command: "composer install --prefer-dist --optimize-autoloader --no-dev",
		},

		"composer-install-dev": {
			command: "composer install",
		},

		"composer-reset-config": {
			command: "git checkout composer.json",
			options: {
				failOnError: false,
			},
		},

		"composer-reset-lock": {
			command: "git checkout composer.lock",
			options: {
				failOnError: false,
			},
		},

		"php-lint": {
			command: "find -L . " +
					 "-path ./vendor -prune -o " +
					 "-path ./vendor_prefixed -prune -o " +
					 "-path ./node_modules -prune -o " +
					 "-path ./artifact -prune -o " +
					 "-name '*.php' -print0 | xargs -0 -n 1 -P 4 php -l",
		},

		phpcs: {
			command: "composer check-cs",
		},
	};
};
