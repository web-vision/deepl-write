#!/usr/bin/env bash

#
# Integration check for the bundled contrib library shipped in `contrib/Libraries`
# for classic mode / TER installations.
#
# It ensures that:
#   1. the version constraint of the bundled package is identical in the root
#      `composer.json` and in `contrib/composer.json`, and
#   2. `contrib/composer.lock` is up to date and resolved with `--prefer-lowest`,
#      so the lowest supported library version is the one shipped.
#
# Run via: Build/Scripts/runTests.sh -s checkContribComposer
#

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../../" && pwd)"
cd "${ROOT_DIR}"

PACKAGE="org_heigl/hyphenator"
LOCK="contrib/composer.lock"
LOCK_BACKUP="contrib/composer.lock.checkorig"

fail() {
    echo "ERROR: $*" >&2
    exit 1
}

echo "[1/2] Checking '${PACKAGE}' constraint parity between composer.json and contrib/composer.json ..."
ROOT_CONSTRAINT="$(php -r '$d = json_decode(file_get_contents("composer.json"), true); echo $d["require"][$argv[1]] ?? "";' "${PACKAGE}")"
CONTRIB_CONSTRAINT="$(php -r '$d = json_decode(file_get_contents("contrib/composer.json"), true); echo $d["require"][$argv[1]] ?? "";' "${PACKAGE}")"

[ -n "${ROOT_CONSTRAINT}" ] || fail "'${PACKAGE}' is not required in composer.json"
[ -n "${CONTRIB_CONSTRAINT}" ] || fail "'${PACKAGE}' is not required in contrib/composer.json"
if [ "${ROOT_CONSTRAINT}" != "${CONTRIB_CONSTRAINT}" ]; then
    echo "  composer.json:         ${ROOT_CONSTRAINT}" >&2
    echo "  contrib/composer.json: ${CONTRIB_CONSTRAINT}" >&2
    fail "'${PACKAGE}' constraint differs between composer.json and contrib/composer.json"
fi
echo "  OK: both pin '${PACKAGE}' to '${ROOT_CONSTRAINT}'"

echo "[2/2] Checking ${LOCK} is up to date (--prefer-lowest) ..."
cp "${LOCK}" "${LOCK_BACKUP}"
trap 'mv -f "${LOCK_BACKUP}" "${LOCK}" 2>/dev/null || true' EXIT
composer update --prefer-lowest --no-install --no-interaction --no-progress -d contrib >/dev/null
if ! diff -q "${LOCK}" "${LOCK_BACKUP}" >/dev/null; then
    echo "--- committed ${LOCK}" >&2
    echo "+++ freshly resolved (--prefer-lowest)" >&2
    diff "${LOCK_BACKUP}" "${LOCK}" >&2 || true
    fail "${LOCK} is out of date. Regenerate it with: composer update --prefer-lowest -d contrib"
fi
echo "  OK: ${LOCK} matches contrib/composer.json (--prefer-lowest)"

echo "SUCCESS"
