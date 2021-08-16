# Rest API with Lumen PHP Framework

[![Build Status](https://travis-ci.org/laravel/lumen-framework.svg)](https://travis-ci.org/laravel/lumen-framework)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel/framework)](https://packagist.org/packages/laravel/lumen-framework)
[![Latest Stable Version](https://img.shields.io/packagist/v/laravel/framework)](https://packagist.org/packages/laravel/lumen-framework)
[![License](https://img.shields.io/packagist/l/laravel/framework)](https://packagist.org/packages/laravel/lumen-framework)

Laravel Lumen Rest API for depots management tech used with docker and swagger for documentation, include: <br>
- Depots 
- Products
- Providers
- Invoices

## Lumen Official Documentation

Documentation for the framework can be found on the [Lumen website](https://lumen.laravel.com/docs).

## Getting Started
First clone the repo: <br>
<code>$ git clone https://github.com/oussemakh1/depot_management_api.git</code>

Install dependencies: <br>
<code>
 $ cd depot_management_api 
 </code>
 <br>
 <code>
 $ composer install 
</code>

Configure the Environment <br>
Create .env file:<br>
<code>$ cat .env.example > .env </code><br>
If you want you can edit database name, database username and database password.

OR With Docker<br>
<code>$ docker-compose build -d</code> <br>

API Documentation && Routes <br>
<code> http://hostname/api/documentation</code>

## License

The Lumen framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
