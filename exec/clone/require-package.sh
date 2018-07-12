#!/usr/bin/env bash

# Clone repository and create symbolic link
# $1 - Working directory
# $2 - Projects directory
# $3 - Package name
# $4 - Addictional composer arguments

##
cd $1
composer require $3 $4
