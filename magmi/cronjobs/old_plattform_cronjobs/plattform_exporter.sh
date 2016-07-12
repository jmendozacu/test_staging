#!/bin/bash

cd /var/www/share/sleepz19.c-111.maxcluster.net/htdocs/shell/

sleep 2

php price_comparison.php --export all --store_view ps_de --ignore_unvisible no #> /dev/null 2>&1

exit 0
