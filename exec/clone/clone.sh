#!/usr/bin/env bash

# Clone repository and create symbolic link
# $1 - Working directory
# $2 - Projects directory
# $3 - Repository url
# $4 - Package name
# $5 - Project name

##
cd $1
mkdir $2 > /dev/null 2>&1
cd $2
git clone $3 $5
cd ..
rm -fr vendor/$4
mv $2/$5 vendor/$4
ln -s ../vendor/$4 $2/$5
