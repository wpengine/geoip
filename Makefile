SHELL := /bin/bash

plugin_name := wpengine-geoip

plugin_dir := /var/www/html/wp-content/plugins/$(plugin_name)/
cd_plugin_dir := cd $(plugin_dir)

make_dir := /var/www/.geoip/
cd_make_dir := cd $(make_dir)

docker_compose := docker-compose -f docker/docker-compose.yml
docker_exec := exec wordpress /bin/bash -c

cli_safe := sudo -u www-data wp
cli_skip := --skip-themes --skip-plugins
cli_path := --path="/var/www/html/"

all: docker_start docker_install_wp docker_test

test: lint smoke unit

docker_start:
	$(docker_compose) up -d --build

docker_stop:
	$(docker_compose) stop

docker_shell:
	$(docker_compose) $(docker_exec) "$(cd_plugin_dir); /bin/bash"

docker_install_wp:
	$(docker_compose) $(docker_exec) "$(cd_make_dir); make install_wp"

docker_test:
	$(docker_compose) $(docker_exec) "$(cd_make_dir); make test"

lint:
	/var/www/.composer/vendor/bin/phpcs --standard=WordPress-Extra,WordPress-Docs --warning-severity=8 tests $(plugin_dir)
	flake8 --max-line-length=120 tests/

smoke:
	pytest -v -r s tests/smoke/

unit:
	/var/www/.composer/vendor/bin/phpunit -c tests/phpunit.xml --testsuite wpengine-geoip-unit-tests

install_wp: setup_core setup_config setup_db

setup_core:
	$(cli_safe) core download $(cli_path) --force

setup_config:
	$(cli_safe) config create $(cli_path) --force \
		--dbname="wordpress" \
		--dbuser="root" \
		--dbpass="password" \
		--dbhost="mysql"

setup_db:
	$(cli_safe) db reset $(cli_path) --yes
	$(cli_safe) core install $(cli_path) --skip-email \
		--url="http://localhost:8080" \
		--title="Test" \
		--admin_user="test" \
		--admin_password="test" \
		--admin_email="test@test.com"
	$(cli_safe) plugin activate $(plugin_name) $(cli_path) --quiet
