#!/usr/bin/env bash

# Clone repository and create symbolic link
# $1 - Working directory
# $1 - Projects directory
# $2 - Package name

##
cd $1

##
if [ -d "vendor/$3" ]; then
    mv vendor/$3 vendor/$3.tmp
fi

##
composer require $3

##
if [ -d "vendor/$3" ]; then
    rm -fr vendor/$3
fi

##
if [ -d "vendor/$3.tmp" ]; then
    mv vendor/$3.tmp vendor/$3
fi
