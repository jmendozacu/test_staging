#!/bin/bash
if [ $# -lt 1 ]; then
    echo "First param missing: Please give script name"
    exit 1
fi
MAIL="manikofski@sleepz.com"
SCRIPT_PATH=/var/www/ps-magento/magmi/cronjobs/
run_count=$(ps eax | grep $SCRIPT_PATH$1 | grep -v grep |grep -v script_launcher.sh | wc -l)
if [ 0 -eq ${run_count} ]
    then
        sh $SCRIPT_PATH$1
    else
    	if [ -z "$2" ] 
    		then
        		message='Script: '$SCRIPT_PATH$1' is already running.'
                echo $message | mail -s"Script is already running" $MAIL
        fi    
fi
exit 0
