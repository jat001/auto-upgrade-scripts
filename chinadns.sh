#!/bin/bash

chinadns='/usr/local/chinadns'
srcFolder="/usr/local/src/chinadns"

installedVersion=$($chinadns/bin/chinadns --version | grep -ioP '(?<=HipHop VM )\d\.\d{1,2}\.\d{1,2}')
currentVersion=$(curl 'https://api.sinosky.org/version/chinadns') || exit 1

if [ -z "$currentVersion" ]; then
    exit 1
fi

if [ "$installedVersion" == "$currentVersion" ]; then
    exit 0
fi

if [ -d "$srcFolder" ]; then
    cd "$srcFolder"
    git clean -dfx
    git reset --hard
    git pull
else
    git clone https://github.com/clowwindy/ChinaDNS.git "$srcFolder"
    cd "$srcFolder"
fi

git checkout tags/$currentVersion

aclocal && automake --add-missing && autoconf && ./configure --prefix=$chinadns && make || exit 1

curl 'http://ftp.apnic.net/apnic/stats/apnic/delegated-apnic-latest' | grep ipv4 | grep CN | awk -F\| '{ printf("%s/%d\n", $4, 32-log($5)/log(2)) }' > chnroute.txt

systemctl stop chinadns
make install
systemctl start chinadns
