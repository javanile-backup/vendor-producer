#!/usr/bin/env bash

# Clone repository and create symbolic link
# $1 - Working directory
# $2 - Projects directory

##
cd $1

##
git add .
git add *
git config push.default simple
git pull
