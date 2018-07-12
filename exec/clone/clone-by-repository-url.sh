#!/usr/bin/env bash

# Clone a repository by url
# $1 - Working directory
# $2 - Projects directory
# $3 - Repository url to clone
# $4 - Project name

##
cd $1
mkdir $2 > /dev/null 2>&1
cd $2
git clone $3 $4
