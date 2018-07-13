#!/usr/bin/env bash

# Clone repository and create symbolic link
# $1 - Working directory
# $2 - Projects directory
# $3 - Package name

##
cd $1

## backup existent vendor
if [ -d "vendor/$3" ]; then
    mv vendor/$3 vendor/$3.tmp
fi

## execute require to build autoload
composer require $3

## replace with backup
if [ -d "vendor/$3.tmp" ]; then
    if [ -d "vendor/$3" ]; then
        rm -fr vendor/$3
    fi
    mv vendor/$3.tmp vendor/$3
fi

## force refresh for missings
composer dump-autoload
