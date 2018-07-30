# Holding at php 7.0 because WP test suite is incompatible with 7.2.
FROM wordpress:php7.0

# Install server dependencies.
RUN apt-get update && apt-get install -qq -y --fix-missing --no-install-recommends \
    build-essential \
    git \
    less \
    libcairo2-dev \
    libgif-dev \
    libjpeg-dev \
    libyaml-dev \
    mysql-client \
    pkg-config \
    python3-pip \
    subversion \
    sudo \
    unzip \
    vim \
    wget \
    zip \
    ;

# Install python testing utils for smokes
RUN pip3 install pytest

# Install phpunit dependencies
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Install WordPress core files and WP Unit Test API
COPY docker/bin/install-wp-tests.sh /tmp/
RUN bash /tmp/install-wp-tests.sh wordpress wordpress password mysql latest true \
    && cp -R /tmp/wordpress/* /var/www/html/

# Install wp-cli
RUN curl -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod 755 /usr/local/bin/wp \
    && mkdir /var/www/.wp-cli/

# Install composer.
COPY docker/bin/install-composer.sh /tmp/
RUN mkdir /var/www/.composer/ && chown www-data:www-data /var/www/.composer/ \
    && bash /tmp/install-composer.sh 

# Install testing dependencies via Composer
RUN sudo -u www-data composer global require \
    phpunit/phpunit \
    squizlabs/php_codesniffer \
    wp-coding-standards/wpcs \
    wimg/php-compatibility

# Install stop-emails mu plugin
COPY docker/bin/stop-emails.php /var/www/html/wp-content/mu-plugins/

# Set permissions on WordPress and WordPress Test Lib directories
RUN chown -R www-data:www-data /var/www/ /tmp/wordpress-tests-lib/
