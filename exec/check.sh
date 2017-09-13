#!/usr/bin/env bash

# Clone repository and create symbolic link
# $1 - working directory
# $2 - package name

##
cd $1

##
$1/vendor/bin/phpcs --report=emacs *.php | sed -e "s@^$1/@@g"
