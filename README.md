# Sprint5

Comandos para ejecutar la api y poder testearla en Postman:

composer install
composer require laravel/passport
php artisan migrate
php artisan key:generate
php artisan passport:keys
php artisan passport:client --personal

El usuario admin es:
    'nickname' => 'Admin'
    'email' => 'hola@admin.com'
    'password' => 'Admin123!' 