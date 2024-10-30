#!/usr/bin/env bash

# Script expects these vars to be present
WORDPRESS_DB_NAME=${WORDPRESS_DB_NAME}
WORDPRESS_DB_USER=${WORDPRESS_DB_USER}
WORDPRESS_DB_PASS=${WORDPRESS_DB_PASS}
WORDPRESS_DB_HOST=${WORDPRESS_DB_HOST}

# Configure test config
WORDPRESS_DIR="/wordpress"
WORDPRESS_TEST_HARNESS_CONFIG="${WORDPRESS_DIR}/wp-tests-config.php"
cp "${WORDPRESS_DIR}/wp-tests-config-sample.php" "${WORDPRESS_TEST_HARNESS_CONFIG}"
sed -i "s/youremptytestdbnamehere/${WORDPRESS_DB_NAME}/" "${WORDPRESS_TEST_HARNESS_CONFIG}"
sed -i "s/yourusernamehere/${WORDPRESS_DB_USER}/" "${WORDPRESS_TEST_HARNESS_CONFIG}"
sed -i "s/yourpasswordhere/${WORDPRESS_DB_PASS}/" "${WORDPRESS_TEST_HARNESS_CONFIG}"
sed -i "s|localhost|${WORDPRESS_DB_HOST}|" "${WORDPRESS_TEST_HARNESS_CONFIG}"