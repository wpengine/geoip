SHELL := /bin/bash

plugin_name := wpengine-geoip
cd_plugin_dir := cd /var/www/html/wp-content/plugins/$(plugin_name)/
docker_exec := docker-compose exec wordpress /bin/bash -c
safe_user := sudo -u www-data
cli_safe := $(safe_user) wp
cli_skip := --skip-themes --skip-plugins

all: docker_start docker_deps docker_install_wp docker_test

test: lint smoke unit integration

docker_start:
	docker-compose -f docker-compose.yml up -d --build

docker_stop:
	docker-compose -f docker-compose.yml stop

docker_shell:
	$(docker_exec) "$(cd_plugin_dir); /bin/bash"

docker_install_wp:
	$(docker_exec) "$(cd_plugin_dir); make install_wp"

docker_deps:
	$(docker_exec) "$(cd_plugin_dir); make deps"

docker_test:
	$(docker_exec) "$(cd_plugin_dir); make test"

deps:
	$(safe_user) composer install
	$(safe_user) vendor/bin/phpcs --config-set installed_paths vendor/wp-coding-standards/wpcs

lint:
	vendor/bin/phpcs --standard=WordPress-Extra,WordPress-Docs --ignore=/vendor/*,/bin/* --warning-severity=8 .
	flake8 --max-line-length=120 bin/ tests/

smoke:
	pytest -v -r s tests/smoke/
	$(safe_user) wp plugin activate $(plugin_name) --quiet

unit:
	vendor/bin/phpunit -c phpunit.xml --testsuite wpengine-geoip-unit-tests

integration:
	vendor/bin/phpunit -c phpunit-integration.xml --testsuite wpengine-geoip-integration-tests

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
