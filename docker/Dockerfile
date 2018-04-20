# Start with the latest WordPress image.
FROM wordpress:4.9.2-php7.0-apache

# Adding nodejs repo
RUN curl -sL https://deb.nodesource.com/setup_8.x | bash

# Install server dependencies.
RUN apt-get update && apt-get install -qq -y --fix-missing --no-install-recommends \
    sudo \
    less \
    nodejs \
    build-essential \
    pkg-config \
    libcairo2-dev \
    libjpeg-dev \
    libgif-dev \
    git \
    subversion \
    mysql-client \
    zip \
    unzip \
    vim \
    libyaml-dev \
    python3-pip \
    ;

# Disable PHP opcache (not great while developing)
RUN rm -rf /usr/local/etc/php/conf.d/opcache-recommended.ini

# Install python testing utils
RUN pip3 install pytest flake8 pylint ddt

# Install phpunit dependencies
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Install WordPress core files and WP Unit Test API
COPY docker/bin/install-wp-tests.sh /
RUN cat /install-wp-tests.sh | bash /dev/stdin wordpress root password mysql latest true

# Install wp-cli
RUN curl -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod 755 /usr/local/bin/wp \
    && mkdir /var/www/.wp-cli/ \
    && chown www-data:www-data /var/www/.wp-cli/

# Install composer.
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer
RUN mkdir /var/www/.composer/ && chown www-data:www-data /var/www/.composer/

# Install testing dependencies via Composer
RUN sudo -u www-data composer global require \
    phpunit/phpunit \
    squizlabs/php_codesniffer \
    wp-coding-standards/wpcs \
    wimg/php-compatibility

# Install stop-emails mu plugin
COPY docker/bin/stop-emails.php /var/www/html/wp-content/mu-plugins/
