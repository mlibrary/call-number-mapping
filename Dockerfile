FROM php:8.3-apache

RUN apt-get update \
 && apt-get upgrade -y \
 && apt-get install -y jq ldap-utils libapache2-mod-authnz-external libapache2-mod-auth-openidc git unzip \
 && apt-get autoremove -y \
 && apt-get clean \
 && (apt-get distclean || rm -rf  /var/cache/apt/archives /var/lib/apt/lists/*) \
 && a2enmod authnz_ldap \
 && mkdir -p /var/cache/apache2/mod_auth_openidc/oidc-sessions /var/cache/apache2/twig /var/www/lib \
 && chown www-data:www-data /var/cache/apache2/mod_auth_openidc/oidc-sessions /var/cache/apache2/twig \
 && docker-php-ext-install pdo_mysql \
 && php -r "copy('https://getcomposer.org/download/latest-stable/composer.phar', '/usr/local/bin/composer');" \
 && chmod +x /usr/local/bin/composer \
 && git config --global --add safe.directory /var/www

COPY auth_openidc.conf /etc/apache2/mods-enabled/auth_openidc.conf
COPY composer.lock composer.json /var/www/

RUN cd /var/www && composer install

COPY src /var/www/src
COPY html /var/www/html
COPY templates /var/www/templates
COPY bin/update-ldap /usr/local/bin/
