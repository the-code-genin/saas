# SaaS Backend

Students as a service backend api.

The api is currently hosted on [https://stuaas.herokuapp.com](https://stuaas.herokuapp.com). The documentation for the api is found [here](documentation.md).

## Requirements

- Apache web server or similar.
- Mysql RDBMS or similar.
- PHP 7.2 or higher.
- Apache's mod_rewrite module enabled or similar in your web server.
- Composer.

## Installation

- Download (or clone the repository **RECOMMENDED**) and place the files in your web server's root directory.
- Create a database for the app.
- Run `composer install`.
- Copy the **.env.example** file to **.env** and configure your app.
- Copy the **.gitignore.example** file to **.gitignore**.
- Run `php vendor/bin/phinx migrate`.
- Configure your web server to direct requests to the **public** folder if the applicable asset file or directory being requested exists in the folder, else, redirect all other requests to the **public/index.php** file **(A sample configuration for apache server has already been provided by default so no need to go through this if you are using apache web server.)**.
- Run `php vendor/bin/phinx seed:run -s SeedOrganizationCategoriesTable`
