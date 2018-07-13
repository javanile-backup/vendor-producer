#!/usr/bin/env bash

# Clone repository and create symbolic link
# $1 - Working directory
# $2 - Projects directory
# $3 - Package name
# $4 - Project name

##
cd $1
mkdir $2 > /dev/null 2>&1
ln -s ../vendor/$3 $2/$4
