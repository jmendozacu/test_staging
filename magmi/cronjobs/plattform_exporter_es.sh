#!/bin/bash

cd /var/www/share/sleepz19.c-111.maxcluster.net/htdocs/shell/

sleep 2

php price_comparison.php --export billiger,ebaycommerce,idealo --store_view es_de --ignore_unvisible no #> /dev/null 2>&1

mv /var/www/share/sleepz19.c-111.maxcluster.net/htdocs/export/price_comparison/tmp/es_de_billiger.csv /var/www/share/sleepz19.c-111.maxcluster.net/htdocs/export/price_comparison/es_de_billiger.csv
mv /var/www/share/sleepz19.c-111.maxcluster.net/htdocs/export/price_comparison/tmp/es_de_ebaycommerce.csv /var/www/share/sleepz19.c-111.maxcluster.net/htdocs/export/price_comparison/es_de_ebaycommerce.csv
mv /var/www/share/sleepz19.c-111.maxcluster.net/htdocs/export/price_comparison/tmp/es_de_idealo.csv /var/www/share/sleepz19.c-111.maxcluster.net/htdocs/export/price_comparison/es_de_idealo.csv

exit 0