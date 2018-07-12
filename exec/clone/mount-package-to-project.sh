#!/usr/bin/env bash

##
# $1 - Working directory
# $2 - Projects directory
# $3 - Require package name
# $4 - Project name
# $5 - Composer addictional arguments

##
cd $1
composer require $3 $5
rm -fr vendor/$3 > /dev/null 2>&1
mkdir -p vendor/$3 > /dev/null 2>&1
mv $2/$4 vendor/$3/..
ln -s ../vendor/$3 $2/$4
