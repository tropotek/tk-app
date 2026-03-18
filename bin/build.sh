#!/bin/bash -e
# Build a docker images and save it as a tar file


SCRIPT=$(realpath "$0")
APP_PATH=$(dirname "$(dirname "$SCRIPT")")
cd "$APP_PATH" || exit 1

docker build -t tk-app:latest -f Dockerfile .
rm -f docker/tk-app_latest.tar.gz
docker save tk-app:latest | pigz > docker/tk-app_latest.tar.gz

