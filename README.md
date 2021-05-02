# LSteam

## Com executar la AC. 
1. Obrir una terminal i buscar la carpeta del projecte
1. <code>docker-compose up -d</code>
1. configurar adminer:
   1. Crear la Base de Dades anomenada "lsteam" amb les taules
    ~~~~
    CREATE DATABASE lsteam;
  
    CREATE TABLE `users` (
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `username` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `password` varchar(255) NOT NULL,
    `birthday` datetime NOT NULL,
    `phone` varchar(20) NOT NULL,
    `profilePic` varchar(255),
    `money` int
    );

    CREATE TABLE `usersPending` (
    `token` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `username` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `password` varchar(255) NOT NULL,
    `birthday` datetime NOT NULL,
    `phone` varchar(20) NOT NULL
    );
   
    CREATE TABLE `ownedGames` (
    `gameId` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `userId` int NOT NULL,
    FOREIGN KEY (userId) REFERENCES users(id)
    );
   
   DROP TABLE IF EXISTS friendRequests;
   CREATE TABLE friendRequests (
   id_orig int,
   id_dest int,
   state int DEFAULT '0',
   accept_time timestamp NULL,
   PRIMARY KEY (id_orig, id_dest),
   FOREIGN KEY (id_orig) REFERENCES users (id),
   FOREIGN KEY (id_dest) REFERENCES users (id)
   );
    ~~~~
4 - sudo chmod 777 public/uploads/
5 - Ja hauria de funcionar tot. 

    Login: http://localhost:8030/login
    Registre: http://localhost:8030/register
    Search: http://localhost:8030/search
    Landing Page: http://localhost:8030/

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

## .env File

Probablement per iniciar el sistema haureu de ficar al .env un format semblant al següent:
```
MYSQL_USER=root
MYSQL_ROOT_PASSWORD=admin
MYSQL_HOST=db
MYSQL_PORT=3306
MYSQL_DATABASE=lsteam
GIPHY_API_KEY=zRrKyJno4h4mhFWgzdT8hyfshV1JUdDw
```
