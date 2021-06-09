#!/usr/bin/env bash

set -e

# Set variables.
PREFIX="refs/tags/"
VERSION=${1#"$PREFIX"}

WP_README_ENV=$2

echo "Building Taro iframe Block v${VERSION}..."

# Install NPM.
npm install
npm run package

# Create README.txt
export WP_README_ENV
curl -L https://raw.githubusercontent.com/fumikito/wp-readme/master/wp-readme.php | php

# Change version string.
sed -i.bak "s/^Version: .*/Version: ${VERSION}/g" ./taro-iframe-block.php
sed -i.bak "s/^Stable Tag: .*/Stable Tag: ${VERSION}/g" ./readme.txt

# Remove unwanted files.
rm -rf .git
rm -rf .github
rm -rf .gitignore
rm -rf .browserslistrc
rm -rf .eslintrc
rm -rf .editorconfig
rm -rf .phpcs.xml
rm -rf .stylelintrc.json
rm -rf .wp-env.json
rm -rf bin
rm -rf node_modules
rm -rf vendor
rm -rf README.md
rm -rf tests
rm -rf phpcs.ruleset.xml
rm -rf phpunit.xml.dist
rm -rf webpack.config.js
rm -rf taro-iframe-block.php.bak
rm -rf readme.txt.bak
