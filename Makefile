SHELL := /bin/bash

plugin_name := wpengine-geoip
plugin_dir := /var/www/html/wp-content/plugins/$(plugin_name)/
cd_plugin_dir := cd $(plugin_dir)
docker_exec := docker-compose exec wordpress /bin/bash -c
cli_root := wp --allow-root
safe_user := sudo -u www-data

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
	$(safe_user) vendor/bin/phpcs --config-set default_standard WordPress-VIP

lint:
	vendor/bin/phpcs --ignore=/vendor/*,/bin/*,/js/* --warning-severity=8 .
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
	$(cli_root) core download --force \
		--path="/var/www/html/"

setup_config:
	$(cli_root) config create --force \
		--dbname="wordpress" \
		--dbuser="root" \
		--dbpass="password" \
		--dbhost="mysql"

setup_db:
	$(cli_root) db reset --yes
	$(cli_root) core install --skip-email \
		--url="http://localhost:8080" \
		--title="Test" \
		--admin_user="test" \
		--admin_password="test" \
		--admin_email="test@test.com"
