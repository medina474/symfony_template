# Symfony

Voici un mode d'emploi pour créer un projet Symfony 8.1. Vous avez deux options, soit développer directement sur votre poste, soit utiliser un environnement de développement conteneurisé.

## Option A : Développement sur le poste local

Installer [Php](https://www.php.net/downloads.php?os=windows&version=8.5) et le [client Symfony](https://github.com/symfony-cli/symfony-cli/releases).

Vérifier les prérequis.

```shell
symfony check:requirements
```

Créer le projet.

```shell
composer create-project symfony/skeleton:"8.1.*" sae5_xxx_yyy
cd sae5_xxx_yyy
```

Il est préférable de partir le gabarit minimal (skeleton), plutôt que sur le gabarit webapp. Il est important de comprendre les différents modules qui seront installés, plutôt que ne pas utiliser ceux présents par défaut parce que l'on ne comprend pas leur utilité.

Afficher les informations du projet en cours.

```shell
php bin/console about
```

Démarrer le serveur web intégré.

```shell
symfony server:start
```

## Option B : Développer dans un Dev Container

Un conteneur de développement (ou dev container) permet d'utiliser **Docker** comme un environnement de développement complet. Il peut servir à exécuter une application, à isoler les outils, les bibliothèques et les environnements d'exécution nécessaires au travail sur un projet en particulier. Il facilite aussi l'intégration continue et les tests.
Les conteneurs de développement peuvent être exécutés localement ou à distance, dans un cloud privé ou public, et sont pris en charge par de nombreux outils et éditeurs ([*Visual Studio Code*](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers) ou [*PhpStorm*](https://www.jetbrains.com/help/phpstorm/connect-to-devcontainer.html)).

L'environnement de développement est **identique** pour tous les membres de l'équipe, quel que soit leur système d'exploitation ou leur configuration locale.

Par exemple le développeur A travaille sous Windows avec Php 8.2, B sous Mac avec Php 8.3 et C sous Linux avec Php 8.4. Avec Dev Container les 3 travaillent avec la même version de Php et la même configuration (php.ini).

Plus d'informations sur le blog de [Stéphane Robert](https://blog.stephane-robert.info/docs/developper/autres-outils/ide/visual-studio-code/devcontainers/)

Ce dépôt utilise le template officiel proposé par *Kévin Dunglas* : [Symfony Docker](https://github.com/dunglas/symfony-docker)

*Kévin Dunglas* est un contributeur actif au projet Symfony,où il a participé à plusieurs composants et améliorations, notamment autour des API, de la sérialisation et des standards du Web.

Ces principales contributions sont :

- [***API Platform***](https://api-platform.com/) : un framework construit sur Symfony permettant de développer rapidement des API REST, GraphQL et, plus récemment, des API temps réel. Il est utilisé par de nombreuses entreprises et administrations.
- [***FrankenPHP***](https://frankenphp.dev/fr/) : un serveur d'applications PHP moderne basé sur Caddy, conçu pour améliorer les performances et simplifier le déploiement des applications PHP. Il prend en charge des fonctionnalités comme les workers, HTTP/2, HTTP/3 et HTTPS automatique. Il est compatible à de nombreux projets *Php* comme *Laravel* et *WordPress*.
- [***Mercure***](https://mercure.rocks/) : un protocole et un hub permettant de diffuser des mises à jour en temps réel vers les navigateurs via les technologies web standard.
- [***Vulcain***](https://vulcain.rocks/) : une proposition visant à optimiser les API hypermédia en réduisant le nombre de requêtes HTTP nécessaires grâce à des mécanismes standardisés.

Télécharger ce dépôt et l'ouvrir avec **Visual Studio Code**, équipé de l'extension [Dev Containers](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers)

> [!WARNING]
> Le conteneur est *rootless* c'est à dire qu'il utilise un utilisateur sans privilège. Mais lors de la construction de l'image certains fichiers ont été crées par l'utilisateur root. Il faut donc réinitialiser le propriétaire pour tous les fichiers.

```shell
docker compose exec php chown -R $(id -u):$(id -g) .
```

Avec Windows depuis une invite de commande dans le dossier du projet.

```shell
docker compose exec php chown -R 1000:1000 .
```

Cette opération est à faire à chaque fois que l'image est reconstruite.

## Composants de développement

```shell
composer require --dev xxx/xxx
```
> [!TIP]
>
> Avec ***Dev Container*** les commandes doivent être exécutées à l'intérieur du conteneur. Ouvrir une fenêtre terminal dans *Visual Studio Code*.

```shell
composer require --dev symfony/maker-bundle symfony/debug-pack symfony/test-pack phpstan/phpstan-symfony
```

***symfony/debug-pack*** fournit un ensemble d’outils de debug (*profiler*, *var-dumper*, logs améliorés) pour analyser le comportement de l’application. Ajoute la célèbre barre de développement de Symfony.

### Symfony Maker

***symfony/maker-bundle*** génère du code standard prêt à être personnalisé (CRUD, entités, contrôleurs) via la ligne de commande. Il standardise la structure et les bonnes pratiques Symfony. Cette commande est très utiles quand il faut créer simultanément plusieurs fichiers liés en même temps.

Lister toutes les commandes associées à *Symfony Maker*.

```shell
php bin/console list make
```

### Symfony Test Pack

***symfony/test-pack*** est un meta-package qui installe et configure les outils de test (PHPUnit) prêts à l’emploi. Il simplifie la mise en place d’une stratégie de tests dans Symfony.

Lancer les tests unitaires

```shell
bin/phpunit
```

### PHPStan

***phpstan/phpstan-symfony*** intègre un outil d'analyse statique du code source afin de détecter les erreurs potentielles.

- vérification stricte des types (beaucoup plus poussée qu'*Intelephense*)
- détection de bugs logiques (*null*, types incohérents, appels impossibles)
- analyse du code sans exécution
- niveau de rigueur configurable (niveau de 0 à 10)
- compréhension avancée de frameworks via des extensions (Symfony, Doctrine, etc.)

Il fonctionne en ligne de commande surtout en intégration continue.

```shell
vendor/bin/phpstan analyse src
```

## Composants frontend

- ***symfony/twig-bundle*** : Intègre le moteur de templates [*Twig*](https://twig.symfony.com/). Il permet de générer les vues HTML et fournit de nombreuses fonctionnalités spécifiques à Symfony.

- ***symfony/asset*** : Fournit des outils pour générer les URLs des ressources statiques (CSS, JavaScript, images). Il facilite également la gestion des versions d'assets pour le cache navigateur.

- ***symfony/asset-mapper*** : Permet de gérer et exposer les fichiers CSS, JavaScript et autres ressources sans avoir besoin d'un bundler comme [*webpack*](https://webpack.js.org/). Il résout automatiquement les dépendances ES Modules et optimise la gestion des assets.

* ***symfony/ux-icons*** : Permet d'utiliser facilement des bibliothèques d'icônes dans les templates Twig. Les icônes sont intégrées comme composants réutilisables et optimisées automatiquement.


```shell
composer require symfony/twig-bundle symfony/asset symfony/asset-mapper symfony/ux-icons
```

Pour trouver une icône à intégrer dans vos *assets*, visitez le site [UX Icons](https://ux.symfony.com/icons).

Pour télécharger l'icône maison de la bibliothèque *Google Material Icon*.

```shell
bin/console ux:icon:import ic:baseline-home
```

### Hotwire

* ***symfony/stimulus-bundle*** intègre la bibliothèque JavaScript [*Hotwire Stimulus*](https://stimulus.hotwired.dev/). Elle ajoute du comportement interactif en reliant des controllers à des éléments DOM de la page HTML via des attributs `data-*`, sans imposer une architecture complète SPA comme *React* ou *Svelte*.

* ***symfony/ux-turbo*** intègre la bibliothèque JavaScript [*Hotwire Turbo*](https://turbo.hotwired.dev/). Elle accélère la navigation en interceptant les requêtes HTTP pour mettre à jour uniquement des fragments HTML au lieu de recharger toute la page, tout en laissant la logique côté serveur.

```shell
composer require symfony/stimulus-bundle symfony/ux-turbo
```

### Tailwind CSS

* ***symfonycasts/tailwind-bundle*** : Intègre la bibliothèque CSS [*Tailwind CSS*](https://tailwindcss.com/) avec une configuration simplifiée. Il permet de compiler automatiquement les styles Tailwind durant le développement et le déploiement.

Télécharger le client *tailwind* et initialiser la configuration.

```shell
php bin/console tailwind:init
```
