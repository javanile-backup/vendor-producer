#!/usr/bin/env bash

# Clone repository and create symbolic link
# $1 - Working directory
# $2 - Projects directory

##
cd $1

## force refresh for missings
composer dump-autoload
