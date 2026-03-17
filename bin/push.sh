#!/bin/bash -e

SCRIPT=$(realpath "$0")
APP_PATH=$(dirname "$(dirname "$SCRIPT")")
cd "$APP_PATH" || exit 1


docker build -t tropotek/tk-app -f docker/Dockerfile .
#docker image ls    # To verify the image exists locally
docker push tropotek/tk-app

#docker build -t tk-app:latest -f docker/Dockerfile .
#rm -f docker/tk-app_latest.tar.gz
#docker save tk-app:latest | pigz > docker/tk-app_latest.tar.gz

