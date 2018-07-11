#!/usr/bin/env bash



##
cd $1
mkdir -p vendor/$2 > /dev/null 2>&1
mv packages/$3 vendor/$2/..
ln -s ../vendor/$2 repository/$3
