#!/bin/bash -e
# Build a release docker image and save it as a tar file

SCRIPT=$(realpath "$0")
APP_PATH=$(dirname "$(dirname "$SCRIPT")")
cd "$APP_PATH" || exit 1

docker build --target release -t tk-app:latest -f Dockerfile .
rm -f docker/tk-app_latest.tar.gz
docker save tk-app:latest | pigz > docker/tk-app_latest.tar.gz

