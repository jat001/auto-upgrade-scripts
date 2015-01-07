#!/bin/bash

nginx='/usr/local/sbin/nginx'

installedVersion=$($nginx -v 2>&1 | grep -ioP '(?<=nginx/)\d\.\d{1,2}\.\d{1,3}')
currentVersion=$(curl 'https://api.sinosky.org/version/nginx') || exit 1

if [ -z "$currentVersion" ]; then
    exit 1
fi

if [ "$installedVersion" == "$currentVersion" ]; then
    exit 0
fi

oldFolderName="nginx-$installedVersion"
newFolderName="nginx-$currentVersion"
file="$newFolderName.tar.gz"

cd /tmp
wget "http://mirrors.sohu.com/nginx/$file" || exit 1
tar zxvpf "$file"
rm -f "$file"
chown -R root:root "$newFolderName"

cd "$newFolderName"
./configure --prefix=/usr/local/nginx --conf-path=/data/etc/nginx/nginx.conf --error-log-path=/data/log/nginx/error.log --http-log-path=/data/log/nginx/access.log --pid-path=/var/run/nginx.pid --lock-path=/tmp/nginx.lock --user=www --group=www --with-ipv6 --with-http_ssl_module --with-http_spdy_module --with-http_gzip_static_module --with-http_stub_status_module --with-http_flv_module --with-http_mp4_module --http-client-body-temp-path=/var/tmp/nginx/client_body --http-proxy-temp-path=/var/tmp/nginx/proxy --http-fastcgi-temp-path=/var/tmp/nginx/fastcgi --http-uwsgi-temp-path=/var/tmp/nginx/uwsgi --http-scgi-temp-path=/var/tmp/nginx/scgi --with-http_sub_module \
&& make && service nginx stop && cp -f objs/nginx /usr/local/nginx/sbin/nginx || exit 1

service nginx start

mv "/tmp/$newFolderName" /usr/local/src
rm -rf "/usr/local/src/$oldFolderName"
