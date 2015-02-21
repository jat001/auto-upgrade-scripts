#!/bin/bash

path='/etc/hosts'
tmpPath='/tmp/hosts'

installedVersion=$(date -d "$(grep -ioP '(?<=# UPDATE: ).+' $path 2>/dev/null)" +%s)
currentVersion=$(curl 'https://api.sinosky.org/version/hosts') || exit 1

if [ -z "$currentVersion" ]; then
    exit 1
fi

if [ "$installedVersion" == "$currentVersion" ]; then
    exit 0
fi

wget 'https://api.sinosky.org/version/hosts/get/dl' -O $tmpPath || exit 1
sed -i '/#TX-HOSTS START/,/#TX-HOSTS END/d' $path
echo '#TX-HOSTS START' >> $path
cat $tmpPath >> $path
echo '#TX-HOSTS END' >> $path
rm -f $tmpPath
