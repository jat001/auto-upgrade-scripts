#!/bin/bash

path='/etc/hosts'
tmpPath='/tmp/hosts'

installedVersion=$(grep -ioP '(?<=#\+UPDATE_TIME )\d{4}(-\d{2}){2} (\d{2}:){2}\d{2}' $path 2>/dev/null)
currentVersion=$(curl 'https://api.sinosky.org/version/hosts') || exit 1

if [ "$installedVersion" == "$currentVersion" ]; then
    exit 0
fi

wget 'https://api.sinosky.org/version/hosts/get/dl' -O $tmpPath || exit 1
sed -i '/#+BEGIN/,/#+END/d' $path
cat $tmpPath >> $path
rm -f $tmpPath
