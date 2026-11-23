FROM php:8.5-apache@sha256:0a785eab8b7eb4836f9c6093771c8ea15ff8ba7269444f3da70a1d33d15fe664


RUN apt-get update \
 && apt-get upgrade -y \
 && apt-get install -y \
     jq ldap-utils libapache2-mod-authnz-external \
     libapache2-mod-auth-openidc git unzip gettext-base \
 && apt-get autoremove -y \
 && apt-get clean \
 && (apt-get distclean || rm -rf  /var/cache/apt/archives /var/lib/apt/lists/*) \
 && a2enmod authnz_ldap \
 && mkdir -p \
     /var/cache/apache2/mod_auth_openidc/oidc-sessions \
     /var/cache/apache2/twig /var/www/lib \
 && chown \
     www-data:www-data \
    /var/cache/apache2/mod_auth_openidc/oidc-sessions \
    /var/cache/apache2/twig \
 && docker-php-ext-install pdo_mysql \
 && docker-php-ext-configure pcntl --enable-pcntl \
 && docker-php-ext-install pcntl \
 && curl \
     -o /usr/local/bin/composer \
     https://getcomposer.org/download/latest-stable/composer.phar \
 && chmod +x /usr/local/bin/composer \
 && git config --global --add safe.directory /var/www \
 && mkdir -p /var/www/empty \
 && echo "session.upload_progress.enabled = 0" > /usr/local/etc/php/conf.d/99-upload-progress.ini

COPY apache/auth_openidc.conf /etc/apache2/mods-enabled/auth_openidc.conf
COPY apache/ports.conf /etc/apache2/ports.conf
COPY apache/001-server-status.conf /etc/apache2/sites-enabled/001-server-status.conf
COPY composer.lock composer.json /var/www/

RUN cd /var/www && composer install

COPY src /var/www/src
COPY html /var/www/html
COPY templates /var/www/templates
COPY bin/update-ldap /usr/local/bin/
COPY bin/build-siteimprove /usr/local/bin/
COPY bin/docker-envsubst-entrypoint /usr/local/bin/docker-envsubst-entrypoint

ENTRYPOINT ["docker-envsubst-entrypoint"]
CMD ["apache2-foreground"]
