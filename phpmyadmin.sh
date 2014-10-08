#!/bin/bash

installedFolder='/home/www/root/default/phpmyadmin'

installedVersion=$(grep -ioP '(?<=Version )\d\.\d{1,2}\.\d{1,3}' $installedFolder/README)
currentVersion=$(curl 'https://api.sinosky.org/version/phpmyadmin') || exit 1

if [ $installedVersion == $currentVersion ]; then
    exit 0
fi

folderName="phpMyAdmin-$currentVersion-all-languages"
file="$folderName.7z"

cd /tmp
wget "http://downloads.sourceforge.net/project/phpmyadmin/phpMyAdmin/$currentVersion/$file" || exit 1
7za x $file
rm -f $file
if [ $installedVersion ]; then
    cp -a $installedFolder/config.inc.php $folderName
fi
rm -rf $installedFolder
mv $folderName $installedFolder
