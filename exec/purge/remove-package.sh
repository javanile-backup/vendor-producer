#!/usr/bin/env bash

# Remove via composer a package
# $1 - Working directory
# $2 - Projects directory
# $3 - Package name

##
cd $1
composer remove $3
rm -fr vendor/$3
sleep 1
