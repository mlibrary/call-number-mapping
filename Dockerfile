FROM php:8.3-apache

RUN apt-get update \
 && apt-get upgrade -y \
 && apt-get install -y libapache2-mod-auth-openidc git \
 && apt-get clean \
 && (apt-get distclean || rm -rf  /var/cache/apt/archives /var/lib/apt/lists/*) \
 && mkdir -p /var/cache/apache2/mod_auth_openidc/oidc-sessions /var/cache/apache2/twig /var/www/lib \
 && chown www-data:www-data /var/cache/apache2/mod_auth_openidc/oidc-sessions /var/cache/apache2/twig \
 && docker-php-ext-install pdo_mysql \
 && php -r "copy('https://getcomposer.org/download/2.7.9/composer.phar', '/usr/local/bin/composer');" \
 && chmod +x /usr/local/bin/composer

COPY auth_openidc.conf /etc/apache2/mods-enabled/auth_openidc.conf
COPY composer.lock composer.json /var/www/

RUN cd /var/www && composer install

COPY src templates html /var/www/
