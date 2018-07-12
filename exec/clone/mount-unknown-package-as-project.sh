#!/usr/bin/env bash

# Clone a repository by url
# $1 - Working directory
# $2 - Projects directory
# $3 - Package name
# $4 - Project name

##
cd $1
mkdir -p vendor/$3 > /dev/null 2>&1
mv $2/$4 vendor/$3/..
ln -s ../vendor/$3 repository/$4
