#!/bin/bash
# https://github.com/facebook/hhvm/wiki/Building-and-Installing-HHVM

hhvm='/usr/local/hhvm'
srcFolder="/usr/local/src/hhvm"

installedVersion=$($hhvm/bin/hhvm --version | grep -ioP '(?<=HipHop VM )\d\.\d{1,2}\.\d{1,2}')
currentVersion=$(curl 'https://api.sinosky.org/version/hhvm') || exit 1

if [ -z "$currentVersion" ]; then
    exit 1
fi

if [ "$installedVersion" == "$currentVersion" ]; then
    exit 0
fi

if [ -d "$srcFolder" ]; then
    cd "$srcFolder"
    rm -rf ./third-party
    git clean -dfx
    git reset --hard
    git pull
else
    git clone https://github.com/facebook/hhvm.git "$srcFolder"
    cd "$srcFolder"
fi

git checkout tags/HHVM-$currentVersion
git submodule update --init --recursive

cmake . -DCMAKE_INSTALL_PREFIX=$hhvm && make || exit 1

service hhvm stop
make install
service hhvm start
