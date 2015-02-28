#!/bin/bash

path='/data/data/ip/qqwry.dat'

currentFileSha1=$(curl 'https://api.sinosky.org/version/czip/sha1') || exit 1

if [ -f "$path" ]; then
    if [ "$(sha1sum $path | cut -d ' ' -f 1)" == "$currentFileSha1" ]; then
        exit 0
    fi

    rm -f "$path"
fi

wget 'https://api.sinosky.org/version/czip/dl' -O "$path" || exit 1
