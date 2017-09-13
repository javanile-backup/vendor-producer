#!/usr/bin/env bash

##
# Test a project
# $1 - Command-line working directory
# $2 - Directory of testing project
# $3 - Relative file or path to test
##

## Move in project directory
cd $2

## Run php unit installed on working directory
$1/vendor/bin/phpunit \
    --configuration phpunit.xml \
    --bootstrap $1/vendor/autoload.php \
    --testdox \
    $3
