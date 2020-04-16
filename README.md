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

- Download and place the applicaton's files in your web server's root directory.
- Create a database for the app.
- Run `composer setup`.
- Run `composer migrate`.
- Run `composer seed -s SeedOrganizationCategoriesTable`.
