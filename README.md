# Snowtricks project
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/3e9cf9664fef484cb603eba79e070462)](https://www.codacy.com/gh/Monsapps/snowtricks/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Monsapps/snowtricks&amp;utm_campaign=Badge_Grade)

## Project structure

```text
./bin/
./config/
./migrations/
./public/
./src/
./templates/
./tests/
./translations/
./.env
./composer.json
./composer.lock
./docker-compose.override.yml
./docker-compose.yml
./phpunit.xml.dist
./README.md (this)
./symfony.lock
```

## Installation

### Installation requirements
-   PHP (>7.3)
-   MySQL (>5.7)
-   Apache (>2.4)
-   Symfony bundle (5.4)
-   Composer (>2.2)

### Step one : install snowtricks dependencies
In your installation directory open terminal and type
```text
composer install
```

### Step two : update .env file
-   Edit .env file with your database server info
```text
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"
```
-   Edit .env file with your smtp info
```text
MAILER_DSN=smtp://localhost
```

### Step three : database
On your terminal
-   Create all tables
```text
php bin/console doctrine:migrations:migrate
```
-   Insert tricks sample to your database
```text
php bin/console doctrine:fixtures:load
```
