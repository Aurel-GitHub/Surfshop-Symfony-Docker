# SurfShop
PHP/Symfony

Hey ! 
To launch the project

- update the db name and password in your .env
- composer install && php bin/console doctrine:database:create &&  php bin/console make:migration && php bin/console doctrine:migrations:migrate
- php -S localhost:8000 -t public

- to test your sending e-mail use http://www.yopmail.com/
- to buy with fake credit card in Stripe use 4242 4242 4242 4242

Waiting to fix the image upload on EasyAdminV3 comment vendor\easycorp\easyadmin-bundle\src\Form\Type\FileUploadType.php  from 161 at 163.
