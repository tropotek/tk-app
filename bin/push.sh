#!/bin/bash -e
#
# Build the Docker image and push it to the docker Tropotek repo
#

IMAGE="tropotek/tk-app"

SCRIPT=$(realpath "$0")
APP_PATH=$(dirname "$(dirname "$SCRIPT")")
cd "$APP_PATH" || exit 1

docker build -t "$IMAGE" -f docker/Dockerfile .
#docker image ls    # To verify the image exists locally
docker push "$IMAGE"


