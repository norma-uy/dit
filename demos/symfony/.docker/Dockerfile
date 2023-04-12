ARG PHP_VERSION

FROM normauy/symfony:6-dev-apache-bullseye
 
COPY ./apache/cmd-vhost.conf /etc/apache2/sites-enabled/000-default.conf
COPY ./apache/apache2.conf /etc/apache2/apache2.conf
COPY ./apache/ports.conf /etc/apache2/ports.conf

ARG WEBSITE_NAME

WORKDIR /var/www/${WEBSITE_NAME}

COPY ./entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]

CMD ["apache2-foreground"]