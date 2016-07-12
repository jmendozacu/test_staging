#!/bin/bash

#
# Sleepz GmbH
#
# Load the export php script for adnymics (ps_de_product_feed.csv)
#
# @category    Sleepz
# @package     Sleepz
# @copyright  Copyright (c) 2016 Sleepz GmbH (http://www.sleepz.com)
#

# set the php cli
PHP_BIN=`which php`

# increase the nice level to -12
NICE_LEVEL="nice --10 "

# Change the diretory to
cd /var/www/sleepz_19/perfekt_schlafen_19/shell/
# wait 2 seconds
sleep 2
# run the product export for adnymcis (create a csv file in {MAGENTO_ROOT/adnymics/export/})
$NICE_LEVEL$PHP_BIN    sleepz2adnymics_api.php -- --export products

exit 0