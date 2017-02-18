#!/usr/bin/env bash

##
cd $1
mkdir repository > /dev/null 2>&1
cd repository
git clone $2 $3

