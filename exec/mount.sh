#!/usr/bin/env bash

# Clone repository and create symbolic link
# $1 - working directory
# $2 - package name
# $3 - project name

##
cd $1
mkdir repository > /dev/null 2>&1
ln -s ../vendor/$2 repository/$3
