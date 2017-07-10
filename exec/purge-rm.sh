#!/usr/bin/env bash

# Remove project folder
# $1 - working directory
# $2 - project name

##
cd $1
rm -fr repository/$2
