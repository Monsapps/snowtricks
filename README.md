# Snowtricks project
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/3e9cf9664fef484cb603eba79e070462)](https://www.codacy.com/gh/Monsapps/snowtricks/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Monsapps/snowtricks&amp;utm_campaign=Badge_Grade)

## Project structure

*   bin (binary files)
*   config (config folder of symfony framework)
*   migrations (database configuration file)
*   public

    *   css (cascading style sheets file)
    *   images
    
     *   avatars (avatars images)
     *   errors (error images)
     *   tricks (tricks images)
    
    *   js (javascript files)

*   src

    *   Controller (controllers file)
    *   DataFixtures (fixtures for dataset)
    *   Entity (entities files)
    *   Event (event files)
    *   Repository (repositories files)
    *   Service (services files)
    *   Subscriber (events subscriber files)
    *   Type (form types files)

*   templates

    *   bundles (templates for errors pages)
    *   trick (templates for tricks pages)
    *   user (templates for users pages)
    *   base.html.twig (common template page)
    *   homepage.html.twig (homepage page)

*   tests (tests files)
*   translations (translations files)
*   .env (config skeleton)
*   composer.json (composer requierement file)
*   composer.lock (composer lock file)
*   docker-compose.override.yml (docker file)
*   docker-compose.yml (docker file)
*   phpunit.xml.dist (phpunit file)
*   README.md (this)
*   symfony.lock (symfony file)

## Installation

### Installation requirements
-   PHP (>7.3)
-   MySQL (>5.7)
-   Apache (>2.4)
-   Symfony bundle (5.4)
-   Composer (>2.2)

### First step : install snowtricks dependencies
In your installation directory open terminal and type
```text
composer install
```

### Second step : Create .env.local file
-   Copy the content of .env file in your new .env.local file
-   Edit .env.local file with your database server info
```text
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"
```
-   Edit .env.local file with your smtp info
```text
MAILER_DSN=smtp://localhost
```

### Third step : database
On your terminal
-   Create all tables
```text
php bin/console doctrine:migrations:migrate
```
-   Insert tricks sample to your database
```text
php bin/console doctrine:fixtures:load
```

### Fourth step: update folders permission
To upload avatars and tricks images inside your server put CHMOD 755 to:
```text
./public/images/avatars
./public/images/tricks
```
