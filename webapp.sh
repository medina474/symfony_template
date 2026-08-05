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

# Composants de communication

composer require \
	symfony/redis-messenger \
	symfony/doctrine-messenger \
	symfony/mercure-bundle \
	symfony/mailer

composer require \
	symfony/uid \
	symfony/serializer-pack
	
# Composants de notification

# You cannot use "Symfony\Bridge\Twig\Mime\NotificationEmail" if the "CSS Inliner" and "Inky" Twig extensions are not available. 
# Try running "composer require twig/cssinliner-extra twig/inky-extra".

composer require \
	symfony/notifier \
	twig/cssinliner-extra \
	twig/inky-extra \
	symfony/ntfy-notifier \
	symfony/free-mobile-notifier
