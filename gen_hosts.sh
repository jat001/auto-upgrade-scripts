#!/bin/bash

redis='/usr/bin/redis-cli -s /tmp/redis.sock'

if [ "$($redis ttl 'hosts')" -gt 518400 ]; then
    exit 0
fi

cd /data/script/google-hosts
git reset --hard
git pull

cd ./scripts
env LC_TIME=en_US.UTF-8 ./auto.sh 216.239.32 64.233.160 66.249.80 72.14.192 209.85.128 66.102 74.125 64.18 207.126.144 173.194

hosts=$(cat ../hosts)
$redis setex 'hosts' 604800 "s:${#hosts}:\"$hosts\";"

rm -r ./output

cd ../
git reset --hard
