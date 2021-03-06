#!/bin/bash

php='/usr/local/php/bin/php'

installedVersion=$($php -v | grep -ioP '(?<=PHP )\d\.\d{1,2}\.\d{1,3}')
currentVersion=$(curl 'https://api.sinosky.org/version/php') || exit 1

if [ -z "$currentVersion" ]; then
    exit 1
fi

if [ "$installedVersion" == "$currentVersion" ]; then
    exit 0
fi

oldFolderName="php-$installedVersion"
newFolderName="php-$currentVersion"
file="$newFolderName.tar.bz2"

cd /tmp
wget "http://mirrors.sohu.com/php/$file" || exit 1
tar xvpf "$file"
rm -f "$file"
chown -R root:root "$newFolderName"

cd "$newFolderName"
./configure --prefix=/usr/local/php --with-config-file-path=/data/etc/php --enable-fpm --with-fpm-user=php --with-fpm-group=php --with-mysql --with-mysqli --with-pdo-mysql --with-gd --with-jpeg-dir --with-png-dir --with-xpm-dir --with-freetype-dir --enable-gd-native-ttf --with-zlib --disable-rpath --enable-bcmath --enable-shmop --enable-sysvsem --with-curl --enable-mbstring --with-mcrypt --enable-ftp --with-openssl --with-mhash --enable-pcntl --enable-sockets --with-xmlrpc --enable-zip --enable-soap --with-gettext --disable-fileinfo --with-bz2 --enable-opcache --with-tidy --with-xsl --with-ldap --enable-exif --enable-calendar --with-pear --enable-wddx --with-imap --with-kerberos --with-imap-ssl --with-sqlite3 --with-pdo-sqlite \
&& sed -ri 's/^(EXTRA_LIBS.+)/\1 -llber/' Makefile && make || exit 1

service php-fpm stop
make install

cd /usr/local/src/php-extension/xcache-3.2.0
/usr/local/php/bin/phpize && ./configure --with-php-config=/usr/local/php/bin/php-config --enable-xcache --enable-xcache-coverager --enable-xcache-optimizer && make && make install

cd /usr/local/src/php-extension/memcached-2.2.0
/usr/local/php/bin/phpize && ./configure --with-php-config=/usr/local/php/bin/php-config --enable-memcached --enable-memcached-json --disable-memcached-sasl --enable-memcached-protocol && make && make install

cd /usr/local/src/php-extension/redis
/usr/local/php/bin/phpize && ./configure --with-php-config=/usr/local/php/bin/php-config --enable-redis && make && make install

service php-fpm start

mv "/tmp/$newFolderName" /usr/local/src
rm -rf "/usr/local/src/$oldFolderName"
