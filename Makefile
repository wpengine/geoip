.PHONY: test build

# User editable vars
PLUGIN_NAME := wpengine-geoip

# Shortcuts
DOCKER_RUN := @docker run --rm -v `pwd`:/workspace
PHPCS_DOCKER_IMAGE := wpengine/phpcs --standard=./test/phpcs.xml --warning-severity=8
WORDPRESS_INTEGRATION_DOCKER_IMAGE := nateinaction/wordpress-integration
COMPOSER_DOCKER_IMAGE := composer
COMPOSER_DIR := -d "/workspace/composer/"

# Commands
all: lint composer_install test

lint:
	$(DOCKER_RUN) $(PHPCS_DOCKER_IMAGE) .

phpcbf:
	$(DOCKER_RUN) --entrypoint "/composer/vendor/bin/phpcbf" $(PHPCS_DOCKER_IMAGE) .

composer_install:
	$(DOCKER_RUN) $(COMPOSER_DOCKER_IMAGE) install $(COMPOSER_DIR)

composer_update:
	$(DOCKER_RUN) $(COMPOSER_DOCKER_IMAGE) update $(COMPOSER_DIR)

test:
	$(DOCKER_RUN) -it $(WORDPRESS_INTEGRATION_DOCKER_IMAGE) "./composer/vendor/bin/phpunit" -c "./test/phpunit.xml" --testsuite="integration"
