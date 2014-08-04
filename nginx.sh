#!/bin/bash

installedVersion=$(/usr/local/sbin/nginx -v 2>&1 | grep -ioP '(?<=nginx/)\d\.\d{1,2}\.\d{1,3}') || exit 1
currentVersion=$(curl 'http://api.sinosky.org/version.php?pro=nginx') || exit 1

if [ $installedVersion == $currentVersion ]; then
    exit 0
fi

oldFolderName="nginx-$installedVersion"
newFolderName="nginx-$currentVersion"
file="$newFolderName.tar.gz"

cd /tmp
wget "http://mirrors.sohu.com/nginx/$file" || exit 1
tar zxvpf $file
rm -f $file
chown -R root:root $newFolderName

cd $newFolderName
./configure --prefix=/usr/local/nginx --conf-path=/home/www/etc/nginx/nginx.conf --error-log-path=/home/www/log/nginx/error.log --http-log-path=/home/www/log/nginx/access.log --pid-path=/var/run/nginx.pid --lock-path=/tmp/nginx.lock --user=www --group=www --with-ipv6 --with-http_ssl_module --with-http_spdy_module --with-http_gzip_static_module --with-http_stub_status_module --with-http_flv_module --with-http_mp4_module --http-client-body-temp-path=/var/tmp/nginx/client_body --http-proxy-temp-path=/var/tmp/nginx/proxy --http-fastcgi-temp-path=/var/tmp/nginx/fastcgi --http-uwsgi-temp-path=/var/tmp/nginx/uwsgi --http-scgi-temp-path=/var/tmp/nginx/scgi --with-http_sub_module \
&& make && cp -f objs/nginx /usr/local/nginx/sbin/nginx || exit 1

mv /tmp/$newFolderName /usr/local/src
rm -rf /usr/local/src/$oldFolderName
