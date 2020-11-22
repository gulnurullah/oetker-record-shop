# Oetker Record Shop
The vintage record shop 'Oldies but Goldies Berlin' needs a new website. 
They want to have all their record collection available for their customers to be consulted online

Customers will be able to see the list of records and search by several fields.
The record list will be maintained by the shop administrators.

## Project Requirements
In order to setup project you can use docker or local enviroment services
* "docker" which's create all required services (PHP 7.4, Mysql , Phpmyadmin)
* You can setup locally PHP 7.4 and mysql without docker

## Setup Project with docker
Go to project root directory and execute by order as below commands
```
docker-compose build
```

```
docker-compose up -d
```

After all services created than you are ready to enter your php container

```
docker exec -it -u dev oetker_record_shop_php bash
```

After you enter your php container than run as below commands
```
composer install
```

To create database and execute migrations run as below commands
```
composer setup:dev
```

Load data fixtures for hotel and reviews
```
php bin/console doctrine:fixtures:load
```


## Get started
After setup the project here we are , its ready to visit on the browser

```
localhost
```

## API Documentation
To see and test api endpoints with nelmio documentation you can visit as below url

```
localhost/api/doc
```

## Tests
In order to run php unit test
```
composer php:unit
```

In order to run phpstan for static code analyse
```
composer php:phpstan
```

## Php Lint
In order to run php lint check
```
composer build:lint
```
