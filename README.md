# Jokes API
## Requirements
PHP 7.4 & Sqlite3

Verify that these PHP extensions are enabled in your .ini files:

    - /etc/php/7.4/cli/php.ini
    - /etc/php/7.4/cli/conf.d/10-opcache.ini
    - /etc/php/7.4/cli/conf.d/10-pdo.ini
    - /etc/php/7.4/cli/conf.d/15-xml.ini
    - /etc/php/7.4/cli/conf.d/20-calendar.ini
    - /etc/php/7.4/cli/conf.d/20-ctype.ini
    - /etc/php/7.4/cli/conf.d/20-curl.ini
    - /etc/php/7.4/cli/conf.d/20-exif.ini
    - /etc/php/7.4/cli/conf.d/20-ffi.ini
    - /etc/php/7.4/cli/conf.d/20-fileinfo.ini
    - /etc/php/7.4/cli/conf.d/20-ftp.ini
    - /etc/php/7.4/cli/conf.d/20-gettext.ini
    - /etc/php/7.4/cli/conf.d/20-iconv.ini
    - /etc/php/7.4/cli/conf.d/20-json.ini
    - /etc/php/7.4/cli/conf.d/20-mbstring.ini
    - /etc/php/7.4/cli/conf.d/20-pdo_sqlite.ini
    - /etc/php/7.4/cli/conf.d/20-phar.ini
    - /etc/php/7.4/cli/conf.d/20-posix.ini
    - /etc/php/7.4/cli/conf.d/20-readline.ini
    - /etc/php/7.4/cli/conf.d/20-shmop.ini
    - /etc/php/7.4/cli/conf.d/20-sockets.ini
    - /etc/php/7.4/cli/conf.d/20-sqlite3.ini
    - /etc/php/7.4/cli/conf.d/20-sysvmsg.ini
    - /etc/php/7.4/cli/conf.d/20-sysvsem.ini
    - /etc/php/7.4/cli/conf.d/20-sysvshm.ini
    - /etc/php/7.4/cli/conf.d/20-tokenizer.ini
    - /etc/php/7.4/cli/conf.d/20-xmlreader.ini,
    - /etc/php/7.4/cli/conf.d/20-xmlwriter.ini,
    - /etc/php/7.4/cli/conf.d/20-xsl.ini,
    - /etc/php/7.4/cli/conf.d/20-zip.ini
    
Use your package manager to install these if needed.

On Debian, for example, you can:

`sudo apt update && sudo apt install -y php7.4 php7.4-cli php7.4-common php7.4-curl php7.4-mbstring sqlite3 \
 php7.4-fpm php-sqlite3 php7.4-xml php7.4-zip`
 
 etc. for any missing extensions.
 
You must have Composer installed.

## Install with Composer
Clone the repo:

`git clone https://github.com/rondobley/symfony-jokes-api.git`
 
Configure your webserver to point to the public directory of the project (`symfony-jokes-api/public`) as the root:

From the root dir of the project run composer:

`composer update --no-dev && composer install --no-dev`

Create the local Sqlite DB:

`php bin/console doctrine:database:create`

Make sure your your new DB in the `var` directory of the project is writable by your webserver.

Then create and run the migrations:

```
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

Then seed the DB with some jokes:

`php bin/console app:initial-seed-db`

Go to `/index.html` to view the Swagger documentation.