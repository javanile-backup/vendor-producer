#!/usr/bin/env bash

# Clone repository and create symbolic link
# $1 - working directory
# $2 - package name

##
cd $1

##
if [ -d "vendor/$2" ]; then
    mv vendor/$2 vendor/$2.tmp
fi

##
composer require $2

##
if [ -d "vendor/$2" ]; then
    rm -fr vendor/$2
fi

##
if [ -d "vendor/$2.tmp" ]; then
    mv vendor/$2.tmp vendor/$2
fi
