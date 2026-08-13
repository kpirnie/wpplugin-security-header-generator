#!/usr/bin/env bash
set -euo pipefail

ROOT="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
SRC="${ROOT}/source"
DIST="${ROOT}/distribute"
SLUG="security-header-generator"
SVN_TRUNK="/home/kpirnie/SVN/${SLUG}/trunk"

# pull the version from the plugin header, and the stable tag from the readme
VERSION="$( grep -m1 '^Version:' "${SRC}/${SLUG}.php" | sed 's/^Version:[[:space:]]*//' | tr -d '\r' )"
STABLE="$( grep -m1 '^Stable tag:' "${SRC}/readme.txt" | sed 's/^Stable tag:[[:space:]]*//' | tr -d '\r' )"

# they have to match, otherwise we are shipping a mismatched release
if [ "${VERSION}" != "${STABLE}" ]; then
    echo "! version mismatch: plugin header ${VERSION} vs readme stable tag ${STABLE}"
    exit 1
fi

echo "# Building ${SLUG} ${VERSION}"

# clean out the distribution
echo "# Cleaning Up Distribution"
rm -rf "${DIST}"
mkdir -p "${DIST}/assets/css" "${DIST}/assets/js" "${DIST}/languages"

# copy the php, the index guards, and the readme
echo "# Working on Templates"
rsync -a --prune-empty-dirs \
    --include='*/' \
    --include='*.php' \
    --exclude='*' \
    "${SRC}/" "${DIST}/"
cp "${SRC}/readme.txt" "${DIST}/readme.txt"

# minify the assets in place, keeping the original filenames
echo "# Working on Assets"
npx --yes esbuild "${SRC}/assets/css/style.css" --minify --outfile="${DIST}/assets/css/style.css" --log-level=warning
npx --yes esbuild "${SRC}/assets/js/script.js" --minify --outfile="${DIST}/assets/js/script.js" --log-level=warning

# ship the composer manifest and build the autoloader against the distributed tree
echo "# Working on Vendor"
cp "${ROOT}/composer.json" "${DIST}/composer.json"
composer install --no-dev --no-interaction --quiet \
    --optimize-autoloader --classmap-authoritative \
    --working-dir="${DIST}"

# generate the translation template
echo "# Working on Languages"
wp i18n make-pot "${DIST}" "${DIST}/languages/${SLUG}.pot" \
    --slug="${SLUG}" \
    --domain="${SLUG}" \
    --exclude=vendor \
    --quiet

# push it to the svn trunk
if [ -d "${SVN_TRUNK}" ]; then
    echo "# Copying to Production"
    rsync -a --delete --exclude='.svn/' "${DIST}/" "${SVN_TRUNK}/"
fi

echo "# Done"
