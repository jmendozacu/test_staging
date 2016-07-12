#!/bin/sh
# Paths
MAGENTO_ROOT=/var/www/share/sleepz19.c-111.maxcluster.net/htdocs/
SOLR_UPDATE_MD="php ${MAGENTO_ROOT}shell/solrbridge_indexer.php --forceindex french"
SOLR_CLEAN_MD="php ${MAGENTO_ROOT}shell/solrbridge_indexer.php --clean french"


$SOLR_UPDATE_MD
sleep 1

$SOLR_CLEAN_MD


exit 0
