# WP Theme Migrator

[![Coding Standards](https://github.com/alleyinteractive/wp-theme-migrator/actions/workflows/coding-standards.yml/badge.svg)](https://github.com/alleyinteractive/wp-theme-migrator/actions/workflows/coding-standards.yml)
[![Testing Suite](https://github.com/alleyinteractive/wp-theme-migrator/actions/workflows/unit-test.yml/badge.svg)](https://github.com/alleyinteractive/wp-theme-migrator/actions/workflows/unit-test.yml)

A WordPress plugin to migrate to a new theme incrementally.

## Installation

You can install the package via composer:

```bash
composer require alleyinteractive/wp-theme-migrator
```

## Usage

Use this package like so:

```php
$package = WP_Theme_Migrator\WP_Theme_Migrator\WP_Theme_Migrator();
$package->perform_magic();
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

This project is actively maintained by [Alley
Interactive](https://github.com/alleyinteractive). Like what you see? [Come work
with us](https://alley.co/careers/).

- [Alley](https://github.com/Alley)
- [All Contributors](../../contributors)

## License

The GNU General Public License (GPL) license. Please see [License File](LICENSE) for more information.