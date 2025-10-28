#!/bin/bash

function exit_container_SIGTERM(){
  echo "Caught SIGTERM"
  exit 0
}
trap exit_container_SIGTERM SIGTERM

echo "Setting /app/public ownership..."
chgrp -R 33 /app
chown -hR 33:33 /app

echo "Starting PHP-FPM..."
php-fpm -F & wait