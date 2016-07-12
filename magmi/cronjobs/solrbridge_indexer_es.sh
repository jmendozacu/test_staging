#!/bin/sh
# Paths
MAGENTO_ROOT=/var/www/share/sleepz19.c-111.maxcluster.net/htdocs/
SOLR_UPDATE_ES="php ${MAGENTO_ROOT}shell/solrbridge_indexer.php --forceindex english"
SOLR_CLEAN_ES="php ${MAGENTO_ROOT}shell/solrbridge_indexer.php --clean english"

$SOLR_UPDATE_ES
sleep 1

$SOLR_CLEAN_ES

exit 0

