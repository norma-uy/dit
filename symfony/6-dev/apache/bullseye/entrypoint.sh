#!/bin/bash
 
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php \
    && mv composer.phar /usr/bin/composer \
    && php -r "unlink('composer-setup.php');" \
    && chmod +x /usr/bin/composer

composer install
yarn install
# yarn build

php bin/console doctrine:migration:migrate --no-interaction
php bin/console doctrine:fixture:load --no-interaction

# a2dissite 000-default.conf
# a2ensite cmd-vhost.conf
 
exec "$@"