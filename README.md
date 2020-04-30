# Stuaas Backend API

Stuaas is a job board website where companies are able to post jobs to be applied for by students, and companies can as well hire students based on their profile.

This repository is the backend API of the project written in php. The API is currently hosted on [https://stuaas.herokuapp.com](https://stuaas.herokuapp.com).

The master branch of the repository should be used for grading.

 ### NOTE:
 We're very enthusiastic about this solution and we want to keep actively working on it, we would keep pushing updates. Any change made after submission will be pushed to the branch ["post-submission".](https://github.com/Iyiola-am/saas/tree/post-submission). So feel free to check our recent progress on the app on the [post-submission branch.](https://github.com/Iyiola-am/saas/tree/post-submission)

## Requirements

- Apache web server or similar.
- MySql RDBMS or similar.
- PHP 7.2 or higher.
- Apache's mod_rewrite module enabled or similar in your web server.
- Composer.

## Installation

- Download and place the applicaton's files in your web server's root directory.
- Redirect all requests to the `public/index.php` file using mod_rewrite or similar. A sample `.htaccess` file has been provided for the apache webserver.
- Create a database for the app.
- Run `composer install` to install all app dependencies.
- Run `php artisan app:setup` to make all necessary files.
- Run `php artisan key:generate` to generate an app key.
- Configure the variables in your `.env` file based on your local environment.
- Run `php artisan app:refresh` to run all app migrations.

## Documentation

All queries to the Stuaas API must be served over `HTTP(S)` and need to be presented in this form: [https://stuaas.herokuapp.com/api/API_VERSION/METHOD_NAME](https://stuaas.herokuapp.com/api/API_VERSION/METHOD_NAME).

For example:

[https://stuaas.herokuapp.com/api/v1/login](https://stuaas.herokuapp.com/api/v1/login).

The supported API request methods are; `GET`, `POST`, `PUT`, `PATCH` and `DELETE`. The `PUT` and `PATCH` methods are essentially the same methods, so the usage is based on user preference. The API is a REST API, so each of the methods specifies the type of action the user wants to do with an API endpoint. User authentication is done through the use of Bearer tokens that are sent with every request.

By default, only `GET` and `POST` are methods are supported by web browsers. To specify the other methods, the base request to the API endpoint must be a `POST` request. Then you add an extra header; `X-Http-Method-Override`. The value of this header is one of `POST`, `PUT`, `PATCH` and `DELETE`. An optional `Content-Type` header with value of `application/json` should also be specified although not compulsory.

For `GET` requests, any additionally information to be passed to the API method should be passed via query strings. For the other request methods, you pass data to the API via the request body. The data being passed should be JSON encoded unless otherwise specified by the API method.

Here is a full explanation on the usage of each request method;

| Method    | Usage                               |
|-----------|-------------------------------------|
| GET       | Retrieve data from an api endpoint. |
| POST      | Create data on an api endpoint.     |
| PUT/PATCH | Update data on an api endpoint.     |
| DELETE    | Delete data on an api endpoint.     |

All responses from the API are also JSON encoded unless otherwise stated by the API.

The API follows the specifications defined [here](https://github.com/Gbahdeyboh/AuthServer) for responses and authentication.

**Below are the documentations for the various API endpoints generated with POSTMAN:**

- [https://documenter.getpostman.com/view/9577513/Szf9XTXH](https://documenter.getpostman.com/view/9577513/Szf9XTXH)
- [https://documenter.getpostman.com/view/9577513/SzS2w8BP](https://documenter.getpostman.com/view/9577513/SzS2w8BP)
- [https://documenter.getpostman.com/view/9577513/SzS2w8BQ](https://documenter.getpostman.com/view/9577513/SzS2w8BQ)
- [https://documenter.getpostman.com/view/9577513/SzKSUfvP](https://documenter.getpostman.com/view/9577513/SzKSUfvP)
- [https://documenter.getpostman.com/view/9577513/Szf9XTXJ](https://documenter.getpostman.com/view/9577513/Szf9XTXJ)
- [https://documenter.getpostman.com/view/9577513/SzKSUfzp](https://documenter.getpostman.com/view/9577513/SzKSUfzp)

## Notes

- You can run `php artisan app:refresh` at any point in time to reset the app back to it's initial state.
