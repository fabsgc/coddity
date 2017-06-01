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

* Modifier le fichier de config app/config/parameters.yml. Fichier utilisé pendant le développement

```
parameters:
    database_host:     127.0.0.1
    database_port:     ~
    database_name:     coddity
    database_user:     root
    database_password: root
        
    secret: fe6f8eea45558973b1d485b1a81e1be05816b8d6
        
    editable.mailer_transport:  smtp
    editable.mailer_host:       127.0.0.1
    editable.mailer_user:       ~
    editable.mailer_password:   ~
    editable.mailer_auth_mode: login
    editable.mailer_port: ~
    editable.mailer_from: contact@agreed.fr
    editable.mailer_from_alias: contact@agreed.fr
```

* Créer la base de données
```
php app/console doctrine:database:create
```

* Lancer la migration
```
php app/console doctrine:schema:update --force
```

* Charger les fixtures pour avoir un jeu de données de base
```
php app/console doctrine:fixtures:load
```

## Compilation des styles

```
cd web/app/dist
compass watch
```

## Comptes utilisables par défaut

```
admin@agreed.fr
password
```

```
fabsgc
password

Ce compte est le créateur des deux sondages
```

```
vinm
password
```

```
qwerty
password
```

## Adresses pour voter sur les deux sondages existants

Sondage pour le choix d'un camping :

```
/web/app_dev.php/answer/1/R6w2QXrqfkYk_jPTaDGo0FqUAZsaL8h3NsUyxZ65GFg
/web/app_dev.php/answer/1/xFomde8U5QjzIvW5QzVzrcokfkP6ifbrnJv0IuyzKP8
/web/app_dev.php/answer/1/IWRz0PTI24pTNo6IKXpRg3ML4I3t2F2voFsXyCCvYzk
```

Sondage pour le choix d'une date pour un barbecue :

```
/web/app_dev.php/answer/2/R6w2QXrqfkYk_jPTaDGo0FqUAZsaL8h3NsUyxZ65GFg
/web/app_dev.php/answer/2/xFomde8U5QjzIvW5QzVzrcokfkP6ifbrnJv0IuyzKP8
/web/app_dev.php/answer/2/IWRz0PTI24pTNo6IKXpRg3ML4I3t2F2voFsXyCCvYzk
```

## Adresses pour voir les résultats 

Sondage pour le choix d'un camping :

```
/web/app_dev.php/results/1/R6w2QXrqfkYk_jPTaDGo0FqUAZsaL8h3NsUyxZ65GFg
```

Sondage pour le choix d'une date pour un barbecue :

```
/web/app_dev.php/results/2/R6w2QXrqfkYk_jPTaDGo0FqUAZsaL8h3NsUyxZ65GFg
```