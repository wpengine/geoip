SHELL := /bin/bash

work_dir = /workspace

plugin_name := wpengine-geoip

plugin_dir := /var/www/html/wp-content/plugins/$(plugin_name)/
cd_plugin_dir := cd $(plugin_dir)

make_dir := /var/www/.geoip/
cd_make_dir := cd $(make_dir)

docker_compose := @docker-compose -f docker/docker-compose.yml
docker_exec := exec wordpress /bin/bash -c
docker_run_silent := @docker run --rm -t

www_data := @sudo -u www-data
cli_skip := --skip-themes --skip-plugins
cli_path := --path="/var/www/html/"

all: docker_start lint docker_install_wp docker_test

lint: lint_yaml lint_markdown lint_python lint_php

test: unit smoke

docker_start:
	$(docker_compose) up -d --build

docker_stop:
	$(docker_compose) stop

docker_clean:
	$(docker_compose) stop | true
	$(docker_compose) rm -v

docker_shell:
	$(docker_compose) $(docker_exec) "$(cd_plugin_dir); /bin/bash"

docker_install_wp:
	$(docker_compose) $(docker_exec) "$(cd_make_dir); make install_wp"

docker_test:
	$(docker_compose) $(docker_exec) "$(cd_make_dir); make test"

lint_php:
	$(docker_compose) $(docker_exec) "/var/www/.composer/vendor/bin/phpcs --standard=$(make_dir)tests/phpcs.xml --warning-severity=8 $(plugin_dir)"

lint_python:
	$(docker_run_silent) -v `pwd`:$(work_dir) wpengine/pylint:latest "$(work_dir)/tests/smoke/" --errors-only

lint_markdown:
	@# exclude MD013 "line too long"
	@# exclude MD024 "allow different nesting"
	$(docker_run_silent) -v `pwd`:$(work_dir) wpengine/mdl:latest "$(work_dir)" --rules ~MD013,~MD024

lint_yaml:
	$(docker_run_silent) -v `pwd`:$(work_dir) wpengine/yamllint:latest "$(work_dir)/docker/"

smoke:
	python3 -m pytest -v -r s "$(make_dir)tests/smoke/"

unit:
	/var/www/.composer/vendor/bin/phpunit -c "$(make_dir)tests/phpunit.xml" --testsuite wpengine-geoip-unit-tests

install_wp: setup_core setup_config setup_db

setup_core:
	$(www_data) wp core download $(cli_path) --force

setup_config:
	$(www_data) wp config create $(cli_path) --force \
		--dbname="${WORDPRESS_DB_NAME}" \
		--dbuser="${WORDPRESS_DB_USER}" \
		--dbpass="${WORDPRESS_DB_PASSWORD}" \
		--dbhost="${WORDPRESS_DB_HOST}"

setup_db:
	$(www_data) wp db reset $(cli_path) --yes
	$(www_data) wp core install $(cli_path) --skip-email \
		--url="http://localhost:8080" \
		--title="Test" \
		--admin_user="test" \
		--admin_password="test" \
		--admin_email="test@test.com"
	$(www_data) wp plugin activate $(plugin_name) $(cli_path) --quiet
