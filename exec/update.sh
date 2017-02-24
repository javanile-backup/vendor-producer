#!/usr/bin/env bash

##
cd $1/repository/$2
ver=$(cat composer.json | grep version | tr -d ',":')
git add .
git add *
git config push.default simple
git pull
