Prérequis :
 - Wampserver3.3.8
  - PHP 8.4.8
  - MySQL 8.0.24
  - Apach 2.4.63.1
  - Adminer 5.3.0
 - Symfony 7
 - Composer

ligne de commande :
 - install scoop 
 - scoop install symfony-cli
 - composer create-project symfony/skeleton Symfony_application
 - cd Symfony_application
 - composer require webapp

 Si le fichier .env et le fichier Kernel.php sont absent :
copier ceux du répertoire. 
ligne de commande :
 - composer install

Remplacer les dossier templates, src, publique du répertoire à votre application
ligne de commande :
- composer require symfonycasts/verify-email-bundle
-  composer require --dev doctrine/doctrine-fixtures-bundle
-  composer require stripe/stripe-php
-  composer require symfony/dotenv --dev
-  npm install bootstrap --save-dev
-  npm install jquery @popperjs/core --save-dev
-  composer require doctrine/doctrine-migrations-bundle
- composer install

Remplacer le fichier config/packages/security config/packages/framework et config/routes/annotations de ce répertoire à votre application
Remplacer le fichier config/services de ce répertoire à votre application

Si le fichier routes et le fichier preload sont absent :
copier ceux du répertoire. 

Connecter vous à votre base de données -> modifier dans env. les données avec les votre ->

APP_SECRET=<your_app_secret_here>
APP_DEBUG=true
DATABASE_URL="mysql://root:<your_database_password>@127.0.0.1:3306/<symfony_project>?serverVersion=8.0.42&charset=utf8mb4"

MAILER_DSN=smtp://<your_email@gmail.com>:<your_email_password>@smtp.gmail.com:587?encryption=tls&auth_mode=login

STRIPE_SECRET_KEY=<your_stripe_secret_key_here>
STRIPE_PUBLIC_KEY=<your_stripe_public_key_here>

ajouter le role ["ROLE_ADMIN"] aux user que vous voulez rendre admin un user dans php adminer 
ligne de commande :
 - php bin/console cache:clear
 - php bin/console doctrine:database:create
 - php bin/console doctrine:migrations:diff
 - php bin/console doctrine:migrations:migrate
 -  php bin/console doctrine:fixtures:load
 - symfony server:start

créer le fichier start.bat puis remplacer le contenu de ce répertoire à celui du votre (pour faire le test au lancement de l'application)
ligne de commande :
 - start.bat
