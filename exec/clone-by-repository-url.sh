#!/usr/bin/env bash

# Clone a repository by url
# $1 - Working directory
# $2 - Repository url to clone
# $3 - Name of package folder

##
cd $1
mkdir packages > /dev/null 2>&1
cd packages
git clone $2 $3
