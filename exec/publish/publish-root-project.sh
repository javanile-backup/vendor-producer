#!/usr/bin/env bash

# Clone repository and create symbolic link
# $1 - Working directory
# $2 - Projects directory
# $3 - Commit message

##
cd $1

if [ -d .git ]; then
    git config credential.helper 'cache --timeout=36000'
    git config push.default simple
    git pull
    git add .
    git add *
    git commit -m "$3"
    git pull
    git push
fi;
