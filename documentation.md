# SaaS API Documentation

Students as a Service.

The api is currently hosted on [https://stuaas.herokuapp.com](https://stuaas.herokuapp.com).

## Making requests

All queries to the SaaS API must be served over `HTTP(S)` and need to be presented in this form: [https://stuaas.herokuapp.com/api/API_VERSION/METHOD_NAME](https://stuaas.herokuapp.com/api/API_VERSION/METHOD_NAME). Like this for example:

[https://stuaas.herokuapp.com/api/v1/login](https://stuaas.herokuapp.com/api/v1/login).

The supported API request methods are; `GET`, `POST`, `PUT`, `PATCH` and `DELETE`. The `PUT` and `PATCH` methods are essentially the same methods, so the usage is based on user preference. The API is a REST API, so each of the methods specifies the type of action the user wants to do with an API method.

By default, only `GET` and `POST` are methods are supported by web browsers. To specify the other methods, the base request to the API endpoint must be a `POST` request, then you add an extra header; `X-Http-Method-Override`, the value of this header is one of `POST`, `PUT`, `PATCH` and `DELETE`. An optional `Content-Type` header with value of `application/json` should also be specified although not compulsory.

For `GET` requests, any additionally information to be passed to the API method should be passed via query strings. For the other request methods, you pass data to the API via the request body. The data being passed should be JSON encoded unless otherwise specified by the API method.

Here is a full explanation on the usage of each request method;

| Method    | Usage                               |
|-----------|-------------------------------------|
| GET       | Retrieve data from an api endpoint. |
| POST      | Create data on an api endpoint.     |
| PUT/PATCh | Update data on an api endpoint.     |
| DELETE    | Delete data on an api endpoint.     |

All responses from the API are also JSON encoded.

The API follows the specifications defined [here](https://github.com/Gbahdeyboh/AuthServer) for responses and authentication.

## API Methods

All requests should be in the form[https://stuaas.herokuapp.com/api/API_VERSION/METHOD_NAME](https://stuaas.herokuapp.com/api/API_VERSION/METHOD_NAME).

### API Version: v1

All requests should be in the form[https://stuaas.herokuapp.com/api/v1/METHOD_NAME](https://stuaas.herokuapp.com/api/v1/METHOD_NAME).

#### Organization Categories

Peform CRUD operations on organization categories.

##### Endpoints

- `GET` organizations/categories
 
 Get the organization categories.

 ###### Request Parameters

 | Field   | Type    | Required | Description                                                                         |
 |---------|---------|----------|-------------------------------------------------------------------------------------|
 | page    | integer | Optional | If specified paginates the results. This sets the current page number.              |
 | perPage | integer | Optional | If specified paginates the results. This sets the maximum number of items per page. |

 ###### Response

 | Field   | Type    | Required | Description                                                                         |
 |---------|---------|----------|-------------------------------------------------------------------------------------|

- `GET` organizations/categories/{id: integer}

 Get the organization categories.

 ###### Response

 | Field   | Type    | Required | Description                                                                         |
 |---------|---------|----------|-------------------------------------------------------------------------------------|
