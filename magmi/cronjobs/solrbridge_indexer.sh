#!/bin/sh
# Paths
MAGENTO_ROOT=/var/www/share/sleepz19.c-111.maxcluster.net/htdocs/
SOLR_UPDATE_PS="php ${MAGENTO_ROOT}shell/solrbridge_indexer.php --forceindex german"
SOLR_CLEAN_PS="php ${MAGENTO_ROOT}shell/solrbridge_indexer.php --clean german"
SOLR_UPDATE_ES="php ${MAGENTO_ROOT}shell/solrbridge_indexer.php --forceindex english"
SOLR_CLEAN_ES="php ${MAGENTO_ROOT}shell/solrbridge_indexer.php --clean english"
SOLR_UPDATE_MD="php ${MAGENTO_ROOT}shell/solrbridge_indexer.php --forceindex french"
SOLR_CLEAN_MD="php ${MAGENTO_ROOT}shell/solrbridge_indexer.php --clean french"

	
$SOLR_UPDATE_PS
sleep 1

$SOLR_CLEAN_PS
sleep 1

$SOLR_UPDATE_ES
sleep 1

$SOLR_CLEAN_ES
sleep 1

$SOLR_UPDATE_MD
sleep 1

$SOLR_CLEAN_MD


exit 0
