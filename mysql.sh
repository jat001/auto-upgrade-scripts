#!/bin/bash

binPath=/usr/local/bin/mysql

installedVersion=$($binPath -V | grep -ioP '(?<=Distrib )\d\.\d{1,2}\.\d{1,3}') || exit 1
currentVersion=$(curl 'http://api.sinosky.org/version.php?pro=mysql') || exit 1

if [ $installedVersion == $currentVersion ]; then
    exit 0
fi

oldFolderName="mysql-$installedVersion"
newFolderName="mysql-$currentVersion"
file="$newFolderName.tar.gz"

cd /tmp
wget "http://mirrors.sohu.com/mysql/MySQL-${currentVersion%.*}/$file" || exit 1
tar zxvpf $file
rm -f $file
chown -R root:root $newFolderName

cd $newFolderName
# https://dev.mysql.com/doc/refman/5.6/en/source-configuration-options.html
cmake -DCMAKE_INSTALL_PREFIX=/usr/local/mysql -DDEFAULT_CHARSET=utf8mb4 -DDEFAULT_COLLATION=utf8mb4_general_ci -DWITH_EMBEDDED_SERVER=1 -DENABLED_LOCAL_INFILE=1 -DMYSQL_DATADIR=/home/mysql -DSYSCONFDIR=/home/www/etc/mysql -DWITH_INNOBASE_STORAGE_ENGINE=1 -DWITH_ARCHIVE_STORAGE_ENGINE=1 -DWITH_BLACKHOLE_STORAGE_ENGINE=1 -DWITH_PERFSCHEMA_STORAGE_ENGINE=1 -DWITH_INNODB_MEMCACHED=1 -DWITH_SSL=yes \
&& make && make install || exit 1

mv /tmp/$newFolderName /usr/local/src
rm -rf /usr/local/src/$oldFolderName
