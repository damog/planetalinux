#!/bin/bash

#
# xiam
#  modificado por damog
#

export PLHOME=/home/planetalinux/
cd $PLHOME/current

git pull origin master

cd /var/www/planetalinux/git

git pull origin master

cd $PLHOME/current/proc

for i in $(find . -maxdepth 1 -type d -iname "[^\.]*");
do
	echo `pwd` $i
	ls $i/config.ini && planetplanet $i/config.ini
#	chmod -R 775 $PLHOME/www/$i*
#	chmod -R 750 $PLHOME/www/principal
done 
fecha=`date +%Y-%m-%d`
echo $fecha
echo "---------------------------"

