#!/usr/bin/env bash

# Clone repository and create symbolic link
# $1 - Working directory
# $2 - Projects directory
# $3 - Project name

##
cd $1/$2/$3

if [ -d .git ]; then
    git config credential.helper 'cache --timeout=36000'
    git add .
    git add *
    git config push.default simple
    git pull
fi;
