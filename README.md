## Com executar la AC. 
1 - Obrir una terminal i buscar la carpeta del projecte<br>
2 - docker-compose up -d<br>
3 - obrir adminer i crear les dues taules.<br>

    CREATE TABLE `User` (
    `id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(255) NOT NULL DEFAULT '',
    `password` VARCHAR(255) NOT NULL DEFAULT '',
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    CREATE TABLE `Search` (
    `id` INT(11) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) unsigned NOT NULL,
    `search` VARCHAR(255) NOT NULL DEFAULT '',
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES User(`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

4 - Ja hauria de funcionar tot. 

    Login: http://localhost:8030/login
    Registre: http://localhost:8030/register
    Search: http://localhost:8030/search

Nota. L'entorn php requereix de la llibreria composer.


## Comandos útils per Docker

Eliminar tots els containers <br>
```
sudo docker rm $(sudo docker ps -aq)
```
<br>
Parar tots els containers <br>
```
sudo docker stop $(sudo docker ps -aq)
```

## Per iniciar el composer
Per iniciar el composer i intalar-ho tot:
```
composer dump-autoload
```
Per carregar el DotENV:
```
composer require "symfony/dotenv"
```
