# Symfony Boilerplate (based on Citadel Framework)

A CoC & easy to deploy framework for building robust and realtime web applications / API's (REST/GraphQL) built on top of Symfony and Mercure.

## Versions

* Pre-alpha: this version is currently under development, it is used on production in Rurusi and Cut developments.

## Parts of the framework

This framework was visualized as an easy way to create a Monolithic application with all the features modern developers would want, feel free to use this to make in record time your MVP.

### Admin panel

This framework comes with EasyAdmin, an advanced tool to create administration backends, this is accessible only to users who have the admin role.

[Easyadmin docs](https://symfony.com/bundles/EasyAdminBundle/current/index.html)

#### Where to modify?

The route and roles configuration is located on config/packages/security.yaml

``` yaml

    # Easy way to control access for large sections of your site
    # Note: Only the *first* access control that matches will be used
    access_control:
        # YOU CAN ADD AS MUCH FILTERS AS YOU WANT.
        #- { path: ^/route, roles: [ROLE_USER] }
        - { path: ^/admin, roles: [ROLE_SUPER_ADMIN, ROLE_ADMIN] }
        - { path: ^/dashboard, roles: [ROLE_STAFF, ROLE_USER] }


```

### TailwindCSS and daisyUI as default CSS framework

TailwindCSS and daisyUI are installed in this distribution to speed up the process of creating interfaces,  you can find the configuration on tailwind.config.js, postcss.config.js and webpack.config.js.

### Frontend asset compiler (Webpack Encore)

### Login and auth

#### Web application

The default credentials for login are email and password, the default user model has email and username enabled by default, to change the login dynamic by default you can alter this field with either username or email to allow users to login.

The default route for the login page is "/login", after succesful login, the users with admin roles "ROLE_SUPER_ADMIN" and "ROLE_ADMIN" will be redirected to the admin dashboard, the other roles are redirected to "/dashboard".

``` yaml 

    #config/packages/security.yaml

    providers:
        # used to reload user from session & other features (e.g. switch_user)
        app_user_provider:
            entity:
                class: App\Entity\User
                property: email

```

And to change the login routes you can alter this configuration:

``` yaml 

    #config/packages/security.yaml

    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false
        main:
            lazy: true
            provider: app_user_provider
            form_login:
                login_path: login
                check_path: login
                enable_csrf: true
            logout:
                path: logout


```

You can find the Login controller on

#### REST Api and graphql WEB AUTHENTICATION REQUIRED TO ACCESS THE DOCS

By default and with scalability in mind the default auth mechanism is using JSON Web Token (JWT), which according to wikipedia and for simplicity's sake is a JSON-based open standard (RFC 7519) for creating access tokens that assert some number of claims. 

First you will have to login on your app, then you can go to '/api' to read the openapi docs, to edit the configuration you can modify this parts of this files.

To authenticate using JWT, first you will have to get a token in '/api/authentication_token', submit a post request like this or use the OpenAPI section to generate it.

``` bash 

#Curl

curl -X 'POST' \
  'http://localhost:8000/api/authentication_token' \
  -H 'accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{
  "email": "johndoe@example.com",
  "password": "apassword"
}'

#Request URL

http://localhost:8000/api/authentication_token

```

##### Request example using the bearer token

``` bash
curl -X 'GET' \
  'http://localhost:8000/api/users/1' \
  -H 'accept: application/ld+json' \
  -H 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2Nzg2NTE5ODQsImV4cCI6MTY3ODY1NTU4NCwicm9sZXMiOlsiUk9MRV9VU0VSIl0sImVtYWlsIjoic3RyaW5nQHN0cmluZy5jb20ifQ.VrOZp5cCd6Bfs1BixwFB1fUEA4Dol7ntlPRr6LuvTxSIfh71Q3a7sLhYJSAvax5zWEsqM8ILXvcfD_P2OT682xTLA_ZrdNEccZ1ERJ7sHiSHGGvg7uTmKxP6AFHsRHYhAFd5WWSkREZClGtVkB0Lo1nSKLJlbiN6guXYC7ifSWuQnRRv7ZFp3PWSsgN8K6zS_zHGDSl0q0UHHMUdk8Bun6SFF-lHCTx-iVkHoHLcJlsqnj5DV3BtQGjDwkQYr7_UK69yZHKpnS6PX7ocp__3IkjBejj4wLKtHVCSbe_FhLm0mNq2kW2ia2sr2aCglx7qVi2xcSvfA3tJNgswkiER6A'
 ```


``` yaml 

# config/packages/api_platform.yaml

api_platform:
    mapping:
        paths: ['%kernel.project_dir%/src/Entity']
    patch_formats:
        json: ['application/merge-patch+json']
    swagger:
        versions: [3]
        api_keys:
             apiKey:
                name: Authorization
                type: header
```

``` yaml 

# config/packages/lexik_jwt_authentication.yaml
lexik_jwt_authentication:
    secret_key: '%env(resolve:JWT_SECRET_KEY)%'
    public_key: '%env(resolve:JWT_PUBLIC_KEY)%'
    pass_phrase: '%env(JWT_PASSPHRASE)%'
    token_ttl: 3600
    user_identity_field: email
```

``` yaml

# config/services.yaml

security:
    # #
    ##
    firewalls:
        dev:
            ##
            ##
        api:
            pattern: ^/api/
            stateless: true
            provider: app_user_provider
            json_login:
                check_path: /api/authentication_token
                username_path: email
                password_path: password
                success_handler: lexik_jwt_authentication.handler.authentication_success
                failure_handler: lexik_jwt_authentication.handler.authentication_failure
            jwt: ~
        main:
            ##
            ##

    # Easy way to control access for large sections of your site
    # Note: Only the *first* access control that matches will be used
    access_control:
        ##
        ##

        # API
        - { path: ^/api/users/register, roles: PUBLIC_ACCESS }
        - { path: ^/api/authentication_token, roles: PUBLIC_ACCESS }
        # List your protected routes like this.
        - { path: ^/api, roles: [ROLE_SUPER_ADMIN, ROLE_ADMIN, ROLE_STAFF, ROLE_USER] }

```

``` yaml 

#config/services.yaml
    App\OpenApi\JwtDecorator:
        decorates: 'api_platform.openapi.factory'
        arguments: ['@.inner'] 

```
You can also modify the login documentation on src/OpenApi/JwtDecorator.php.

### Web profiler

The Symfony's web profiler is enabled by default when the env is development.

### REST API

if your app instance is running go to /api, only authenticated users can access the docs!.

### GraphQL

If you use an integer to query the data it will throw an error, this is because API platform id's are represented as an [IRI (Internationalized Resource Identifier)](https://www.w3.org/TR/ld-glossary/#internationalized-resource-identifier), to query data, the url prefix for the IRI must always be "/api/{resource}/{id?}".

take for example this query to select the email and roles of a user, you can paste this query for you to test it! (after you created your account of course).

``` graphql 

query {
  user(id: "api/users/1") {
    email,
    roles
  }
}

```

You can read more about it [here!](https://api-platform.com/docs/core/graphql/#queries).

### Template engine

By default Twig is included.

### Realtime updates

API platform supports mercure, this enables you to make realtime API's.

### Automatic forms creation

You can easily create forms with the included makerfile.

## Roadmap

- Add commands to setup the application framework.
- Add a version that integrates OpenSwoole to make a high performace framework.

## Getting Started

Everything has been pre-configured for you, so you don't have to do break your head doing it, we have included bob "the builder" cli tool as an abstraction layer on top of symfony as well as custom commands to execute batch commands.

### Advanced ussage

For experienced developers who want to customize the framework feel free to read the docs on the tools used (go to built with).

### Prerequisites

- PHP >= 8.1
- Composer
- npm
- MySQL/PostgresSQL driver or SQLITE
- Symfony cli (Optional)
- Docker (Optional)
- LAMP/WAMP dev server (Optional)

### Installing

Citadel comes with a powerful command to generate all migrations and compile resources.

NOTE: We must have our database connection on our .env file set up.

##### Configuring yor database

[To store sessions in redis and use MongoDB as well as advanced configuration click here](https://symfony.com/doc/current/session/database.html)

As previously discussed, one important aspect to take into account is the database you will be using, you can of course use non relational databases like MongoDB and redis, nonetheless the default configuration is for relational databases.

The default database is SQLITE, to start using your desired database driver you should modify first your driver for doctrine in your .env file:

```
###> doctrine/doctrine-bundle ###
# Format described at https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url
# IMPORTANT: You MUST configure your server version, either here or in config/packages/doctrine.yaml
#
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
# DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7&charset=utf8mb4"
# DATABASE_URL="postgresql://symfony:ChangeMe@127.0.0.1:5432/app?serverVersion=13&charset=utf8"
###< doctrine/doctrine-bundle ###

 ```

Then before building your app, you need to add the configuration in the only migration your migrations folder, do this by commenting the database drivers you will not use and leave uncommented the one you are using.

``` php

        //SQLITE CONFIG
        $this->addSql(
            'CREATE TABLE sessions (
                sess_id VARCHAR(128) NOT NULL PRIMARY KEY,
                sess_data BLOB NOT NULL,
                sess_lifetime INTEGER NOT NULL,
                sess_time INTEGER NOT NULL
            );
            CREATE INDEX sessions_sess_lifetime_idx ON sessions (sess_lifetime);'
        );

        // MYSQL/MARIADB CONFIG
        /*
        $this->addSql(
            'CREATE TABLE `sessions` (
                `sess_id` VARBINARY(128) NOT NULL PRIMARY KEY,
                `sess_data` BLOB NOT NULL,
                `sess_lifetime` INTEGER UNSIGNED NOT NULL,
                `sess_time` INTEGER UNSIGNED NOT NULL,
                INDEX `sessions_sess_lifetime_idx` (`sess_lifetime`)
            ) COLLATE utf8mb4_bin, ENGINE = InnoDB;'
        );
        */

        // POSTGRESQL CONFIG
        /*
        $this->addSql(
            'CREATE TABLE sessions (
                sess_id VARCHAR(128) NOT NULL PRIMARY KEY,
                sess_data BYTEA NOT NULL,
                sess_lifetime INTEGER NOT NULL,
                sess_time INTEGER NOT NULL
            );
            CREATE INDEX sessions_sess_lifetime_idx ON sessions (sess_lifetime);'
        );
        */

        // MICROSOFT SQL SERVER CONFIG
        /*
        $this->addSql(
            'CREATE TABLE sessions (
                sess_id VARCHAR(128) NOT NULL PRIMARY KEY,
                sess_data NVARCHAR(MAX) NOT NULL,
                sess_lifetime INTEGER NOT NULL,
                sess_time INTEGER NOT NULL,
                INDEX sessions_sess_lifetime_idx (sess_lifetime)
            );'
        );
        */

```

##### Building using bob cli tool UNDER DEVELOPMENT, PLEASE RUN THE MANUAL INSTALLATION PROCESS

This framework comes with server database sessions for the web application and JWT auth for the REST API/GraphQL Api powered by Api platform, to build the app for development execute:

``` cmd

# Example for Linux

# Collect all the dependencies

user@machine:~Path/$ composer install

# Then build the application

user@machine:~Path/$ php bob build:dev


```

##### Building from scratch using Symfony

You can use bob to execute this commands, nonetheless, we will remove al the boilerplate and show you how to build by using makerbundle.

###### Install all the composer dependencies

``` cmd

user@machine:~Path/$ composer install

```

###### Generate keys

App Secret

``` cmd
user@machine:~Path/$ php bin/console build:app-secret

 ```


JWT keys

``` cmd 

user@machine:~Path/$ php bin/console lexik:jwt:generate-keypair

```

###### Generate the initial migration in the database

``` cmd

# Make a migration first
user@machine:~Path/$ php bin/console make:migration

# Execute it
user@machine:~Path/$ php bin/console doctrine:migrations:migrate

```

###### Build your assets

``` cmd

user@machine:~Path/$ npm run dev

```

###### Create an admin user to access the admin dashboard

``` cmd

user@machine:~Path/$ php bob new:admin

```

## Citadel Commands

Along with all the MakerBundle Commands [Listed here](https://symfony.com/bundles/SymfonyMakerBundle/current/index.html), we included some commands, and expect to add more in the future to reduce boilerplate.

### Execute command

``` cmd 
user@machine:~Path/$ php bob [COMMAND] [ARG?] [--OPTION?]

```

### Commands

| command | description |
| ------- | ----------- |
| build:dev | builds the application for development. |
| build:prod | builds the application for deployment. |
| build:app-secret | creates a new APP_SECRET |
| new:admin | generates a new user with admin role. |
| serve | starts a minimal dev server. |


## Development server


### Symfony CLI server

If you have the [Symfony CLI](https://symfony.com/doc/current/setup/symfony_server.html) installed, you can run:

``` cmd

user@machine:~Path/$ symfony server:start

 ```

### Citadel minimal local server

This minimal local server helps you to develop you app using a local server.

``` cmd

user@machine:~Path/$ php bob serve

```

## Running the tests

Please read this [documentation](https://symfony.com/doc/current/testing.html)


### And coding style tests

We strongly recommend using PSR-4 and PSR-12 coding standards

- [PSR-4](https://www.php-fig.org/psr/psr-4/)
- [PSR-12](https://www.php-fig.org/psr/psr-12/)

## Deployment

You can find a detailed documentation [here](https://symfony.com/doc/current/setup/web_server_configuration.html).

### On apache

Deploying to apache and shared hosting has been pre-configured, as you can see we have apache pack and .htaccess with the configuration on it on the project.

[Read more]()

#### Shared hosting

This project comes with all the configuration needed to deploy it on shared hosting, you just have to upload the project to your public_html file and don't forget to configure php version >= 8.1.

### On NGINX

[Deployment on NGINX](https://www.nginx.com/resources/wiki/start/topics/recipes/symfony/)

[Symfony's docs](https://symfony.com/doc/current/setup/web_server_configuration.html#nginx)

### On Docker

[Deployment on Docker](https://symfony.com/doc/current/setup/docker.html)

### On Swoole

[Swoole integration](https://openswoole.com/article/symfony-swoole)

[Symfony runtime component](https://symfony.com/doc/current/components/runtime.html)

## Built With

* [Symfony](https://symfony.com/) - Symfony is a set of reusable PHP components and a PHP framework for web projects.
* [API Platform](https://api-platform.com) - API Platform is the most advanced API platform, in any framework or language.
* [MakerBundle](https://symfony.com/bundles/SymfonyMakerBundle/current/index.html) - Symfony Maker helps you create empty commands, controllers, form classes, tests and more so you can forget about writing boilerplate code.
* [EasyAdmin](https://symfony.com/bundles/EasyAdminBundle/current/index.html) - Creates beautiful administration backends for your Symfony applications. It's free, fast and fully documented.
* [NPM](https://www.npmjs.com/) - JavaScript Package Manager, Registry & Website.

## Complements

* [Mercure](https://mercure.rocks/) - Mercure is an open protocol for real-time communications designed to be fast, reliable and battery-efficient.

## Contributing

Reach to us via github for details on our code of conduct, and the process for submitting pull requests to us.

## Versioning

Pending, we are currently on our alpha version.

## Authors

* **Jose Alejandro Melendez G.** - Software engineer/CEO Rurusi - [Rurusi](https://rurusi.co)

See also the list of [contributors](https://github.com/jalexmelendez/citadel-framework/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE) file for details

## Acknowledgments

This tools motivated the creation of this framework, check them out!

* [Django](https://www.djangoproject.com/) - Django is a high-level Python web framework that encourages rapid development and clean, pragmatic design.
* [Rails](https://rubyonrails.org/) - Rails is a full-stack framework. It ships with all the tools needed to build amazing web apps on both the front and back end.
* [Laravel](https://laravel.com/) - The PHP Framework for Web Artisans.