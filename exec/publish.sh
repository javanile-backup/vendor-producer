#!/usr/bin/env bash

##
cd $1/repository/$2
git config push.default simple
git pull
git add .
git add *
git commit -m "$3"
git pull
git push