#!/usr/bin/env bash

docker build -t "smartassert/basil-worker-web:${TAG_NAME:-master}" .
