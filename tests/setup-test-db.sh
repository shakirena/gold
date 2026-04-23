#!/usr/bin/env bash
# Setup yii2_basic_tests database for unit/functional tests.
# Run once on a new machine before running: php vendor/bin/codecept run unit
#
# Usage:
#   bash tests/setup-test-db.sh
#   bash tests/setup-test-db.sh -h 127.0.0.1 -u root -p secret
#
# Requirements: mysql and mysqldump in PATH, gold DB must exist.

set -e

HOST="${MYSQL_HOST:-localhost}"
USER="${MYSQL_USER:-root}"
PASS="${MYSQL_PASS:-}"
SOURCE_DB="gold"
TEST_DB="yii2_basic_tests"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
FIXTURE_SQL="$SCRIPT_DIR/_data/test-fixtures.sql"

if [ -n "$PASS" ]; then
    MYSQL_CMD="mysql -h$HOST -u$USER -p$PASS"
    DUMP_CMD="mysqldump -h$HOST -u$USER -p$PASS"
else
    MYSQL_CMD="mysql -h$HOST -u$USER"
    DUMP_CMD="mysqldump -h$HOST -u$USER"
fi

echo "[1/3] Creating database '$TEST_DB'..."
$MYSQL_CMD -e "CREATE DATABASE IF NOT EXISTS $TEST_DB CHARACTER SET utf8 COLLATE utf8_unicode_ci;"

echo "[2/3] Copying schema from '$SOURCE_DB' (structure only, no data)..."
$DUMP_CMD --no-data "$SOURCE_DB" | $MYSQL_CMD "$TEST_DB"

echo "[3/3] Inserting test fixtures..."
$MYSQL_CMD "$TEST_DB" < "$FIXTURE_SQL"

echo "Done. Run: php vendor/bin/codecept run unit"
