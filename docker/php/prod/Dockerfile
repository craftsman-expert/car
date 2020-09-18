FROM php:7.4-fpm

RUN mkdir -p /www \
      && mkdir -p /www/var/log \
      && mkdir -p /www/var/cache \
      && mkdir -p /www/media

WORKDIR /www

# Download script to install PHP extensions and dependencies
ADD https://raw.githubusercontent.com/mlocati/docker-php-extension-installer/master/install-php-extensions /usr/local/bin/

RUN chmod uga+x /usr/local/bin/install-php-extensions && sync

RUN DEBIAN_FRONTEND=noninteractive apt-get update -q \
    && DEBIAN_FRONTEND=noninteractive apt-get install -qq -y \
      curl \
      git \
      zip unzip \
    && install-php-extensions \
      bcmath \
      bz2 \
      calendar \
      exif \
      gd \
      intl \
      ldap \
      mysqli \
      opcache \
      pdo_mysql \
      xsl \
      zip \
      sockets \
    && docker-php-ext-enable sockets
# already installed:
#      iconv \
#      mbstring \

# Install Composer.
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && ln -s $(composer config --global home) /root/composer
ENV PATH=$PATH:/root/composer/vendor/bin COMPOSER_ALLOW_SUPERUSER=1

# Install prestissimo (composer plugin). Plugin that downloads packages in parallel to speed up the installation process
# After release of Composer 2.x, remove prestissimo, because parallelism already merged into Composer 2.x branch:
# https://github.com/composer/composer/pull/7904
RUN composer global require hirak/prestissimo

COPY composer*.json ./
RUN composer install
COPY . .
RUN composer dump-env prod

RUN chmod -R 777 /www/var \
    && rm -Rf /www/config/packages \
    && rm -Rf /www/config/routes \
    && rm -f /www/config/*.yaml \
    && rm -f /www/config/bundles.php \
    && chmod -R 777 /www/media
