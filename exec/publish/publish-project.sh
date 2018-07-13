#!/usr/bin/env bash

# Clone repository and create symbolic link
# $1 - Working directory
# $2 - Projects directory
# $3 - Project name
# $4 - Commit message

##
cd $1/$2/$3

if [ -d .git ]; then
    git config push.default simple
    git pull
    git add .
    git add *
    git commit -m "$4"
    git pull
    git push
fi;
