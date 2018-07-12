#!/usr/bin/env bash

# Remove project folder
# $1 - Working directory
# $2 - Projects directory
# $3 - Project name

##
cd $1
rm -fr $2/$3
sleep 1
