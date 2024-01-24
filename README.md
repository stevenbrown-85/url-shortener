# Url Shortener

The brief is to build a very simple url shortening service, similar to something like bit.ly or goo.gl.

The purpose of this assessment is to see how you approach a problem, and to assess your technical ability in object oriented php as well as your understanding of the http protocol. 

Your solution should be built using PHP with a MySQL database.

You may use php packages via composer, however we have provided a scaffold package which you can use if you wish.

The scaffold package uses [Nette Database](https://doc.nette.org/en/database) for database abstraction, and [Symfony HTTP Foundation](https://symfony.com/doc/current/components/http_foundation.html) to manage requests and responses in a nice object-oriented way.

## Requirements

### 1 - Create a short code from a url
A user must be able to get a short code from a url. Ideally, there should be checks in place to ensure a given url is valid and resolves.

Request:
```
GET /shorten?url=google.com HTTP/1.1
```

Response:
```
Host: localhost:8000
Response:
HTTP/1.1 200 OK
Content-Type: text/plain
http://localhost:8000/a7F15gaw
```

### 2 - Get a url from a short code

A user must be able to be get a long url from a given short code. Each time a short code is requested, the hit counter for that url should be updated.

Request:
```
GET /a7F15gaw
```
```
HTTP/1.1
Host: localhost:8000
Response:
HTTP/1.1 302 Found
Location: http://google.com
```
### 3 - Get stats about a shortcode

Request:
```
GET /stats?short_code=a7F15gaw
```
Response:

```
HTTP/1.1
Host: localhost:8000
Response:
HTTP/1.1 200 OK
Content-Type: text/plain
Hits: 1
Last accessed: 2024-01-24 12:51:02
```

## Getting started

These steps are only applicable if you choose to use the provided scaffold application.

1. Copy the .env.example file `cp .env.example > .env`
2. Install composer dependencies
3. Build docker (see below)
4. Run the db seeder, if using docker: `docker exec php-app php migrate.php`

### Building Docker 

Run `docker-compose build app` to build the app image.

Run `docker-compose up -d` to start the server

If you are running this demo on your local machine, use http://localhost:8000 to access the application from your browser.

## Submission

Either commit your work to (public) github repo and provide us with a url, or if you prefer, you can zip up your solution and email it to us.

### Bonus - Running Tests

To run tests you can either run them locally, or use docker.

`./vendor/bin/phpunit tests`

or 

`docker exec php-app ./vendor/bin/phpunit tests`
