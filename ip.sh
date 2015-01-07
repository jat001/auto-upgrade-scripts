#!/bin/bash

redis='/usr/bin/redis-cli -s /tmp/redis.sock'

conf='/data/etc/nginx/vhost/pi.conf'

oldIP=$(grep -ioP '(?<=proxy_pass http://)(\d{1,3}\.){3}\d{1,3}' $conf 2>/dev/null)

if [ "$($redis exists 'ip')" == 1 ]; then
    newIP=$($redis get 'ip')
fi

if [ -z "$oldIP" ] || [ -z "$newIP" ]; then
    exit 1
fi

if [ "$oldIP" == "$newIP" ]; then
    exit 0
fi

oldIP=$(echo "$oldIP" | sed 's/\./\./')
sed -i "s/$oldIP/$newIP/" $conf >/dev/null 2>&1 || exit 1

service nginx reload >/dev/null 2>&1 || exit 1
