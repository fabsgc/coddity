# coddity
Développement d'une plateforme web pour permettre le vote d'un groupe d'amis sur différents topics.

# Environnement

* php 7.0
* php_pdo
* php_intl
* php_fileinfo
* php_mcrypt
* php_mysql

# Installation

## Récupération des dépendances

```
    composer install
```

## Préparation des assets

```
    php app/console assets:install
```

## Migration de la database

* Modifier le fichier de config app/config/parameters.yml

* Créer la base de données
```
    php app/console doctrine:database:create
```

* Lancer la migration
```
    php app/console doctrine:schema:update --force
```

* [Optionnel] Charger les fixtures
```
    php app/console doctrine:fixtures:load
```

## Compilation des styles

```
    cd web/app/dist
    compass watch
```