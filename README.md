## Kinglimousine
##### run and follow these commands: 
- Create a database locally named `kinglimousine` utf8_general_ci 
- Download composer https://getcomposer.org/download/
- Pull Laravel/php project from git provider i.e. `git clone https://github.com/testingatswt/kinglimousine.git`.
- Copy `.env.backup` file to `.env`inside your project root and fill the database information.
  (windows wont let you do it, so you have to open your console cd your project root directory and run `cp .env.backup .env` )
- Open the console and cd your project root directory
- Run `composer install` or ```php composer.phar install```
- Run `php artisan key:generate` 
- Run `php artisan migrate`
- Run `php artisan db:seed` to run seeders, if any.
- Run `php artisan storage:link` to link storage.

- Run `php artisan config:cache` (this will faster the application).
---
