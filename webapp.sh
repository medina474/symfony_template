#!/bin/sh

composer require \
	symfony/monolog-bundle

# Composants de développement
composer require --dev \
	symfony/maker-bundle \
	symfony/debug-pack \
	symfony/test-pack \
	phpstan/phpstan-symfony

# Composants frontend
composer require \
	symfony/twig-bundle \
	symfony/asset \
	symfony/asset-mapper \
	symfony/ux-icons

bin/console make:controller HomeController

# Interactivité
composer require \
	symfony/stimulus-bundle \
	symfony/ux-turbo \
	symfonycasts/tailwind-bundle

bin/console tailwind:init

# Composants backend

composer require \
	symfony/orm-pack \
	symfony/form \
	symfony/validator \
	martin-georgiev/postgresql-for-doctrine

composer require --dev \
	phpstan/phpstan-doctrine

composer require \
	symfony/security-bundle

