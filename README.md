# WP Theme Migrator

[![Coding Standards](https://github.com/alleyinteractive/wp-theme-migrator/actions/workflows/coding-standards.yml/badge.svg)](https://github.com/alleyinteractive/wp-theme-migrator/actions/workflows/coding-standards.yml)
[![Testing Suite](https://github.com/alleyinteractive/wp-theme-migrator/actions/workflows/unit-test.yml/badge.svg)](https://github.com/alleyinteractive/wp-theme-migrator/actions/workflows/unit-test.yml)

A library to support agile, incremental theme migrations in WordPress.

## Background

This package provides a library that facilitates a stepwise approach to migrating a WordPress site to a new theme.

The conventional strategy for re-theming a site is to build an entire theme, then activate the new theme on the production environment when it's complete. This library enables you to use [a Strangler Fig pattern](https://martinfowler.com/bliki/StranglerFigApplication.html) to move gradually from an old theme to a new theme with both themes installed on the production environment.

The parameters of the migration strategy are passed via callbacks to the Migrator during initialization. The Migrator parses the current request early and passes the query vars to each callback so you can base the migration strategy on post type, taxonomy, publish date, language, post meta or any public query var that is added before the Migrator is run.

Furthermore, you can define your migration strategy on more than just what's available in the query. The Migrator is agnostic – it only needs to know whether the current request has been migrated to the new theme. So your strategy can draw on globals, constants, an API integration, the day of the week – or any value that's available when the Migrator is run – to determine migratability.

With thoughtfully structured callbacks, you can even A/B test redesigned pages during development. Individual content types can be built, tested, and released before the theme is complete, bringing a truly iterative cycle to your workflow.

## Releases

This package is released via Packagist for installation via Composer. It follows semantic versioning conventions.

## Roadmap

This package is in a pre-release status. Milestones to be completed before the first release include:

- Adding sidebar and menu support.
- Adding theme mod support.
- Adding feature testing.


### Install

Requires Composer and PHP >= 8.0.


### Use

Install a new valid theme. To be valid, it must exist in the `/wp-content/themes/` directory, be compatible with the local WordPress and PHP versions, and include at least a `style.css` file. Do not activate the new theme.

Install this package via Composer.

```sh
composer require alleyinteractive/wp-theme-migrator
```

Ensure that the Composer autoloader is loaded into your project.

```php
require_once __DIR__ . '/vendor/autoload.php';
```

Initialize the Migrator in your project. The Migrator performs its magic on the `setup_theme` hook so it must be initialized before that. Here, the Migrator object is created on the `plugins_loaded` hook:

```php
/**
* Initialize WP Theme Migrator early.
*/
function init_migrator() {
	try {
		$migrator = new \Alley\WP\Theme_Migrator\Migrator();
		$migrator->init();
	} catch( Exception  $e ) {
		// Do something. The Migrator will throw an Exception when it's
		// initialized with an invalid theme or callback. Be sure to catch
		// the Exception to avoid a fatal error.
	}
}
add_action( 'plugins_loaded', 'init_migrator' );
```

Pass the name of the new theme and a list of one or more callbacks to the Migrator. A callback must return `true` if a given request should be handled with the new theme. If more than one callback is provided, the Migrator will call each one once until one returns `true`. Then, the Migrator will load the new theme and no more callbacks will be called on that request. If none of the provided callbacks return `true`, the old theme will be loaded.

```php
/**
* Add context for WP Theme Migrator.
*
* @param array    $context  Array of context values.
* @param Migrator $migrator Migrator instance.
*/
function filter_wp_theme_migrator_context( $context, $migrator) {
	return [
		'theme'     => 'new-theme-slug',
		'callbacks' => [
			'a_callback', // This can be any callable.
			'another_callback',
		],
	];
}
add_filter( 'wp_theme_migrator_context', 'filter_wp_theme_migrator_context', 10, 2 );
```

Define your migration strategy through your callbacks.
```php
/**
* Callback to manage theme migration.
*
* @param array $query_vars Array of query vars for the current request.
* @return bool Whether to load the new theme.
*/
function a_callback( $query_vars ): bool {
	// Do something to decide if the current request is migratable.
}
```

Once you've migrated the entire site, activate your new theme, remove this package from your project, and uninstall your old theme.

### From Source

To work on this project locally, first add the repository to your project's
`composer.json`:

```json
{
	"repositories": [
		{
			"type": "path",
			"url": "../path/to/wp-theme-migrator",
			"options": {
				"symlink": true
			}
		}
	]
}
```

Next, add the local development files to the `require` section of
`composer.json`:

```json
{
	"require": {
		"alleyinteractive/wp-theme-migrator": "@dev"
	}
}
```

Finally, update composer to use the local copy of the package:

```sh
composer update alleyinteractive/wp-theme-migrator --prefer-source
```

### Changelog

This project keeps a [changelog](CHANGELOG.md).


## Development Process

See instructions above on installing from source. Pull requests are welcome from the community and will be considered for inclusion. Releases follow semantic versioning and are shipped on an as-needed basis.


### Contributing

See [our contributor guidelines](CONTRIBUTING.md) for instructions on how to contribute to this open source project.


## Project Structure

This is a Composer package that is published to [Packagist](https://packagist.org/). Classes are autoloadable using `alleyinteractive/composer-wordpress-autoloader`. They live in the `src` directory and follow standard WordPress naming conventions for classes.


## Third-Party Dependencies

Dependencies are managed by Composer, and include:

- `alleyinteractive/composer-wordpress-autoloader`: Used for autoloading classes that follow the standard WordPress conventions for filenames.
- `alleyinteractive/alley-coding-standards`: Used for running phpcs linting.
- `mantle-framework/testkit`: Used for running unit tests.
- `symfony/filesystem`: Used for copying files into the WordPress test installation for testing.


## Maintainers

- [Alley](https://github.com/alleyinteractive)

![Alley logo](https://avatars.githubusercontent.com/u/1733454?s=200&v=4)

### Contributors

Thanks to all of the [contributors](../../contributors) to this project.


## License

This project is licensed under the
[GNU Public License (GPL) version 2](LICENSE) or later.
