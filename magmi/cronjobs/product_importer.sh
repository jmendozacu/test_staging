#!/bin/sh
# Paths
MAGENTO_ROOT=/var/www/share/sleepz19.c-111.maxcluster.net/htdocs/
IMPORT_PATH="$MAGENTO_ROOT"var/import/
INDEXER="php ${MAGENTO_ROOT}shell/indexer.php --reindexall"
MAGMI_CONFIG_PATH="$MAGENTO_ROOT"magmi/conf/
MAIN_IMPORTER_NAME=ps_product_importer
MAGMI_CLI="$MAGENTO_ROOT"magmi/cli/magmi.cli.php
[ ! -d "$IMPORT_PATH" ] && (echo "Export path does not exist"; exit 1)
IMPORTING=false
cd "$IMPORT_PATH"
YEAR_FOLDER=`date +%Y`
WEEK_FOLDER=`date +%V`
NEW_FILE_PREFIX=`date +%s`
if [ ! -d $YEAR_FOLDER ]
	then
	mkdir $YEAR_FOLDER
fi
if [ ! -d "$YEAR_FOLDER"/"$WEEK_FOLDER" ]
		then
		mkdir "$YEAR_FOLDER"/"$WEEK_FOLDER"
fi
for FILE in * ; do
		if [[ "$FILE" =~ full_update_marketplace_id_([0-9]+)_attribute_set_id_([0-9]+).csv ]]
			then
			MARKETPLACE=${BASH_REMATCH[1]}
			ATTRIBUTE_SET=${BASH_REMATCH[2]}
			IMPORTING=true
			if [ -d "$MAGMI_CONFIG_PATH""$MAIN_IMPORTER_NAME"_marketplace_"$MARKETPLACE"_attribute_set_"$ATTRIBUTE_SET" ]; then
				IMPORTER="$MAIN_IMPORTER_NAME"_marketplace_"$MARKETPLACE"_attribute_set_"$ATTRIBUTE_SET"
			elif [ -d "$MAGMI_CONFIG_PATH""$MAIN_IMPORTER_NAME"_attribute_set_"$ATTRIBUTE_SET" ]; then
				IMPORTER="$MAIN_IMPORTER_NAME"_attribute_set_"$ATTRIBUTE_SET"
			elif [ -d "$MAGMI_CONFIG_PATH""$MAIN_IMPORTER_NAME"_marketplace_"$MARKETPLACE" ]; then
				IMPORTER="$MAIN_IMPORTER_NAME"_marketplace_"$MARKETPLACE"
			elif [ -d "$MAGMI_CONFIG_PATH""$MAIN_IMPORTER_NAME" ]; then
				IMPORTER="$MAIN_IMPORTER_NAME"
			fi
			php "$MAGMI_CLI" -profile="$IMPORTER" -mode=create -CSV:filename="$IMPORT_PATH""$FILE"
			sleep 1
			mv "$IMPORT_PATH""$FILE" "$IMPORT_PATH""$YEAR_FOLDER"/"$WEEK_FOLDER"/"$NEW_FILE_PREFIX""$FILE"
		else
			echo "$FILE does not match regex."
		fi
done
if $IMPORTING
	then
	sleep 1
	$INDEXER
fi
exit
