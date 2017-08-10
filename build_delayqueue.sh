#!/usr/bin/env bash

wget -c https://github.com/ouqiang/delay-queue/releases/download/v0.3/delay-queue-linux-amd64.tar.gz
tar xzf delay-queue-linux-amd64.tar.gz
cd delay-queue

./delay-queue &> /dev/null &