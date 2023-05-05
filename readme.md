# Quai Antique #
`Note pour l'inspecteur à la fin du fichier readme.md`

Pour installer ce site en local veuillez suivre les étapes suivantes :

## 1- Configurez votre environnement local : 
Vous avez besoin : 
* PHP 8.1.10
* Symfony 5.4.19
* Node.js
* Composer 2.5.4
* XAMPP ou Laravel

## 2- Clonez le dépît GitHub 
Dans votre terminal:

```  
git clone https://github.com/Coussecousse/quai_antique 
```
Puis replacez vous dans le nouveau dossier ainsi créé.

## 3- Créez le fichier .env 
Créez un fichier .env et mettez dedans :  
```
APP_ENV=dev
APP_SECRET=

MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0

DATABASE_URL="mysql://root:@127.0.0.1:3306/quai_antique?serverVersion=mariadb-10.4.11&charset=utf8mb4"

MAILER_DSN=smtp://

```

Pour l'APP_SECRET vous pouvez taper :
```
openssl rand -hex 32
```
Et mettre la valeur dans votre `APP_SECRET`.
Pensez à configurer votre connexion à votre base de données et pour le `MAILER_DSN` je vous conseille [Mailtrap](https://mailtrap.io/).

## 3- Installez les dépendances 
Dans votre terminal : 
```
composer install 
```

## 4- Créez la base de donnée et créez les tables 
Accédez à votre base de donnée et tapez la commande SQL : 
```
CREATE DATABASE quai_antique;
```
Puis tapez la commande :
```
php bin/console doctrine:migrations:diff
```
Et enfin :
```
php bin/console doctrine:migrations:migrate
```
## 5- Insérez des données 
Vous avez la possibilité de rentrer des 'fausses' données pour le site internet.
Ces données sont situées dans le dossier `src/DataFixtures/` et sont :
* CarouselFixtures.pop
* FoodFixtures.php
* MenuFixtures.php

Si vous souhaitez débuter avec un site vierge, vous pouvez les supprimer. 

Dans ce dossier vous possédez un fichier 'AdminFixtures'
C'est dans ce dossier que vous pouvez configurer vos identifiants admin.
Vous pouvez changer le `motdepasse` et l'email `admin@gmail.com`.

Par la suite vous pouvez taper dans le terminal : 
```
php bin/console doctrine:fixtures:load
```
Pensez à confirmer l'ajout des données.

## 6- Lancez le serveur 
Maintenant, vous pouvez lancer :
```
symfony server:start 
```
### 6.1- Soucis avec intl 
Si vous décidez d'utilise XAMPP il se peut que vous ayez des soucis avec `intl`.
Dans ce cas là lancez votre application XAMPP puis allez dans `Apache>Config>php.ini`

Une fois dans le fichier .txt vous pouvez `CTRL + F` et taper `intl`. 
Par la suite dès que vous trouvez un point virgule devant la ligne vous devez l'enlever
```
extension=intl
...
[intl]
intl.default_locale = fr
 This directive allows you to produce PHP errors when some error
 happens within intl functions. The value is the level of the error produced.
 Default is 0, which does not produce any errors.
intl.error_level = E_WARNING
intl.use_exceptions = 0
```

## 7- Vous êtes fin prêt !
Votre site en local est prêt ! 

Le site internet est disponible ici : [Quai-antique](https://quai-antique.fr/)
_____________________

`Pour l'inspecteur :`

Ayant oublié de vous partager les logins pour vous connecter en tant qu'administrateur, je les mets exceptionnellement ici :

Email : `admin23z2qVjQ@gmail.com`

Mot de passe : `weFyyW8338yNU2`

Ces informations seront supprimées dès que j'aurais reçu un retour.
__________________