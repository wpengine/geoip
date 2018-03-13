SHELL := /bin/bash

plugin_name := wpengine-geoip
cd_plugin_dir := cd /var/www/html/wp-content/plugins/$(plugin_name)/
docker_compose := docker-compose -f docker/docker-compose.yml
docker_exec := exec wordpress /bin/bash -c
safe_user := sudo -u www-data
cli_safe := $(safe_user) wp
cli_skip := --skip-themes --skip-plugins

all: docker_start docker_install_wp docker_test

test: lint smoke unit integration

docker_start:
	$(docker_compose) up -d --build

docker_stop:
	$(docker_compose) stop

docker_shell:
	$(docker_compose) $(docker_exec) "$(cd_plugin_dir); /bin/bash"

docker_install_wp:
	$(docker_compose) $(docker_exec) "$(cd_plugin_dir); make install_wp"

docker_test:
	$(docker_compose) $(docker_exec) "$(cd_plugin_dir); make test"

lint:
	/var/www/.composer/vendor/bin/phpcs --standard=WordPress-Extra,WordPress-Docs --ignore=/bin/* --warning-severity=8 .
	flake8 --max-line-length=120 tests/

smoke:
	pytest -v -r s tests/smoke/
	$(safe_user) wp plugin activate $(plugin_name) --quiet

unit:
	/var/www/.composer/vendor/bin/phpunit -c tests/phpunit.xml --testsuite wpengine-geoip-unit-tests

integration:
	/var/www/.composer/vendor/bin/phpunit -c tests/phpunit-integration.xml --testsuite wpengine-geoip-integration-tests

install_wp: setup_core setup_config setup_db

setup_core:
	$(cli_safe) core download --force \
		--path="/var/www/html/"

setup_config:
	$(cli_safe) config create --force \
		--dbname="wordpress" \
		--dbuser="root" \
		--dbpass="password" \
		--dbhost="mysql"

setup_db:
	$(cli_safe) db reset --yes
	$(cli_safe) core install --skip-email \
		--url="http://localhost:8080" \
		--title="Test" \
		--admin_user="test" \
		--admin_password="test" \
		--admin_email="test@test.com"
	$(cli_safe) plugin activate $(plugin_name) --quiet
