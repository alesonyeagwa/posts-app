# Posts APP API

## About
This is a simple API application developed with Laravel 8 for the creation and management of posts/articles. It provides the functionality of the creation of user accounts that will be used to create and publish posts. It also permits comments on each post from other users. This project implements the JSON:API specifications.

## All Features
- User account registration
- Creation and management of posts
- Access to published posts by other users
- Create and manage comments on published posts
- Access to all published posts by a user
- Access to all published comments by a user

## Installation
- Clone repository
```
$ git clone https://github.com/alesonyeagwa/posts-app.git
```
- Run in your terminal
```
$ composer install
$ php artisan key:generate
```

- Setup database connection in .env file
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=root
DB_USERNAME=
DB_PASSWORD=secret
```
- Migrate tables
```
$ php artisan migrate
```
- Access it on
```
http://127.0.0.1:8000/api/v1
```

Full API documentation is available on
[Read the Docs](https://documenter.getpostman.com/view/4507352/TzRLmVzC).

## Issues

If you discover an issue within this project, please you're more than welcome to submit a pull request, or if you're not feeling up to it - create an issue so someone else can pick it up.

## License

This project is an open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
