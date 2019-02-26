#!/usr/bin/env bash
mode=${1:-""}
if [[ "watch" == "$mode" ]]; then
    docker-compose -f docker-compose.yml -f docker-compose.dev.yml up --build
elif [[ "rebuild" == "$mode" ]]; then
    docker-compose -f docker-compose.yml -f docker-compose.dev.yml build --no-cache && \
    docker-compose -f docker-compose.yml -f docker-compose.dev.yml up -d --force-recreate
else
    docker-compose -f docker-compose.yml -f docker-compose.dev.yml up --build -d
fi

