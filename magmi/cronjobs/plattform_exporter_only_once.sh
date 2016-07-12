#!/bin/bash

cd /var/www/share/sleepz19.c-111.maxcluster.net/htdocs/shell/

sleep 2

php price_comparison.php --export idealo --store_view md_de --ignore_unvisible no #> /dev/null 2>&1

#mv /var/www/share/sleepz19.c-111.maxcluster.net/htdocs/export/price_comparison/tmp/md_de_idealo.csv /var/www/share/sleepz19.c-111.maxcluster.net/htdocs/export/price_comparison/md_de_idealo.csv


exit 0