#!/bin/sh
# Paths

#kill the old process
#kill `ps -ef | grep "forceindex german" | grep -v grep | awk '{print $2}'`
#kill `ps -ef | grep "solrbridge_indexer_ps.sh" | grep -v grep | awk '{print $2}'`

MAGENTO_ROOT=/var/www/share/sleepz19.c-111.maxcluster.net/htdocs/
SOLR_UPDATE_PS="php ${MAGENTO_ROOT}shell/solrbridge_indexer.php --forceindex german"
SOLR_CLEAN_PS="php ${MAGENTO_ROOT}shell/solrbridge_indexer.php --clean german"

$SOLR_UPDATE_PS
sleep 1

$SOLR_CLEAN_PS


exit 0
