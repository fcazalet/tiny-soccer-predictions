#!/bin/sh

chown -R www:www /var/www/html

exec "$@"