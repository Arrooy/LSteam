## Com executar la AC. 
1 - Obrir una terminal i buscar la carpeta del projecte
2 - docker-compose up -d
3 - obrir adminer i crear les dues taules.

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


;Adria Arroyo.