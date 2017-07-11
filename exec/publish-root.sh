#!/usr/bin/env bash

##
cd $1
git config push.default simple
git pull
git add .
git add *
git commit -m "$2"
git pull
git push