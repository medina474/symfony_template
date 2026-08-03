#!/bin/sh

composer require \
	symfony/monolog-bundle

# Composants de développement
composer require --dev \
	symfony/maker-bundle \
	symfony/debug-pack \
	symfony/test-pack \
	phpstan/phpstan-symfony
