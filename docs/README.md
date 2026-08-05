# Symfony Docker

Un installeur et environnement d'exécution du framework [Symfony](https://symfony.com) basé sur [Docker](https://www.docker.com/), intégrant [FrankenPHP](https://frankenphp.dev) et [Caddy](https://caddyserver.com/).

Compatible avec les agents de développement IA : il inclut un [Dev Container](https://containers.dev/) ainsi qu'un [guide d'une page](agents.md) expliquant comment utiliser [OpenCode](https://opencode.ai),
[Claude Code](https://claude.ai/claude-code) ou tout autre assistant de développement basé sur l'IA, avec un modèle local ou distant, et la possibilité d'activer un bac à sable réseau (network sandbox) optionnel.

![CI](https://github.com/dunglas/symfony-docker/workflows/CI/badge.svg)

## Prise en main

1. Si ce n'est pas déjà fait, installez [Docker Compose](https://docs.docker.com/compose/install/) (version 2.10 ou supérieure).
2. Exécutez `docker compose build --pull --no-cache` afin de reconstruire les images Docker à partir des dernières versions disponibles.
3. Exécutez `docker compose up --wait` pour créer et démarrer une nouvelle instance d'un projet Symfony.
4. Ouvrez `https://localhost` dans votre navigateur Web préféré, puis acceptez le [certificat TLS auto-généré](https://stackoverflow.com/a/15076602/1352334).
5. Lorsque vous avez terminé, exécutez `docker compose down --remove-orphans` pour arrêter les conteneurs Docker et supprimer les conteneurs orphelins.

## Fonctionnalités

- Prêt pour la **production**, le **développement** et l'**intégration continue (CI)**
- Un seul service Docker par défaut
- Configuration claire et facile à lire
- Performances très élevées grâce au [mode Worker de FrankenPHP](https://frankenphp.dev/docs/worker/)
- [Installation de services Docker Compose supplémentaires](extra-services.md) avec **Symfony Flex**
- HTTPS automatique (en développement comme en production)
- Prise en charge de HTTP/3 et des [Early Hints](https://symfony.com/blog/new-in-symfony-6-3-early-hints) support
- Messagerie en temps réel grâce à un [hub Mercure](https://symfony.com/doc/current/mercure.html) intégré
- Prise en charge de [Vulcain](https://vulcain.rocks)
- Intégration native de [XDebug](xdebug.md)
- [Rechargement à chaud (Hot Reload)](https://frankenphp.dev/docs/hot-reload/)
- Prise en charge des [Dev Containers](https://containers.dev/)
- Compatibilité avec les [agents de développement IA](agents.md), avec un bac à sable réseau (sandbox) optionnel
- Image de production légère (slim) et exécutable sans privilèges (rootless)

**Bon développement !**

## Documentation

1. [Options disponibles](options.md)
2. [Utiliser Symfony Docker avec un projet existant](existing-project.md)
3. [Support des services supplémentaires](extra-services.md)
4. [Déploiement en production](production.md)
5. [Débogage avec Xdebug](xdebug.md)
6. [Certificats TLS](tls.md)
7. [Utiliser MySQL à la place de PostgreSQL](mysql.md)
8. [Utiliser Alpine Linux à la place de Debian](alpine.md)
9. [Utilisation d’un Makefile](makefile.md)
10. [Mise à jour du modèle (template)](updating.md)
11. [Dépannage](troubleshooting.md)
12. [Utilisation d’agents de développement IA](agents.md)

## Licence

Symfony Docker est distribué sous la licence MIT.

## Crédits

Créé par [Kévin Dunglas](https://dunglas.dev), co-maintenu par [Maxime Helias](https://twitter.com/maxhelias) et soutenu par [Les-Tilleuls.coop](https://les-tilleuls.coop).
