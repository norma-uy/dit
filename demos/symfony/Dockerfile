ARG PHP_VERSION

FROM php:${PHP_VERSION}-apache-bullseye

RUN a2enmod rewrite
 
RUN apt-get update \
  && apt-get install -y libzip-dev zlib1g-dev libicu-dev g++ icu-devtools libonig-dev \
    gnupg libpng-dev libfreetype6-dev libjpeg62-turbo-dev libwebp-dev \ 
    git wget curl sendmail --no-install-recommends \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
 
RUN docker-php-ext-install pdo mysqli pdo_mysql zip mbstring;
RUN docker-php-ext-configure gd --with-freetype --with-webp --with-jpeg \ 
  && docker-php-ext-install gd
RUN docker-php-ext-configure intl \
  && docker-php-ext-install intl 

RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
RUN apt-get -y install nodejs
RUN npm install -g yarn
 
COPY ./docker/apache/cmd-vhost.conf /etc/apache2/sites-enabled/000-default.conf
COPY ./docker/apache/apache2.conf /etc/apache2/apache2.conf
COPY ./docker/apache/ports.conf /etc/apache2/ports.conf

# COPY ./docker/php/php.ini /usr/local/etc/php/php.ini

ARG WEBSITE_NAME

WORKDIR /var/www/${WEBSITE_NAME}

COPY . /var/www/${WEBSITE_NAME}
# RUN chown -R www-data:www-data /var/www/${WEBSITE_NAME}
# RUN chmod -R 775 /var/www/${WEBSITE_NAME}
# RUN usermod -a -G www-data ${USER}

COPY ./docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]

CMD ["apache2-foreground"]