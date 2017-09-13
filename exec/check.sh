#!/usr/bin/env bash

# Clone repository and create symbolic link
# $1 - working directory
# $2 - package name

##
cd $1

##
$1/vendor/bin/phpcs \
    --ignore=*/tests/*,*/vendor/* \
    --report=emacs . | sed -e "s@^$1/@@g"
