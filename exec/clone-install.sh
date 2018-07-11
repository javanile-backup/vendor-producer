#!/usr/bin/env bash

##
# $1 - Working directory
# $2 - Require package name
# $3 - Project name
# $4 - Composer addictional arguments

##
cd $1
composer require $2 $4
rm -fr vendor/$2 > /dev/null 2>&1
mkdir -p vendor/$2 > /dev/null 2>&1
mv repository/$3 vendor/$2/..
ln -s ../vendor/$2 repository/$3
