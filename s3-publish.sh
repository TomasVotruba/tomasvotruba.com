#!/bin/bash

if [ ! -f "s3.conf" ]
then
    echo "Unable to locate s3.conf file - use s3.conf.dist as a template for configuring your S3 settings."
    exit 1
else
    # Load config file
    . s3.conf
fi

if [ ! -z "$1" -a "$1" != "--dry-run" -a "$1" != "-n" ]
then
    echo "Usage: ./publish.sh [--dry-run|-n]"
    exit 2
fi

# Would be a good idea to wipe out the prod env first, so that DELETE_REMOVED works properly
# If you're comfortable with that, uncomment the line below:
# rm -rf output_prod/*

vendor/bin/sculpin generate --env=prod || ( echo "Could not generate the site" && exit )

S3CMD_PATH=`which s3cmd`
if [ $? -ne 0 -o -z "$S3CMD_PATH" ]
then
    echo "s3cmd not found - unable to deploy"
    exit 3
fi

if [ ! -f "$S3_CONFIG" ]
then
    echo "Unable to find s3cmd config file - unable to deploy"
    exit 4
fi

if [ "$S3_DELETE" = "true" ]
then
    echo "Enabling DELETE_REMOVED"
    DELETE_REMOVED='--delete-removed'
else
    echo "Disabling DELETE_REMOVED"
    DELETE_REMOVED='--no-delete-removed'
fi

if [ "$S3_REGION" = "" ]
then
    S3_REGION=US
fi

if [ "$1" = "--dry-run" -o "$1" = "-n" ]
then
    DRY_RUN='--dry-run'
else
    DRY_RUN=''
fi

s3cmd --config="$S3_CONFIG" $DRY_RUN --force --recursive $DELETE_REMOVED --bucket-location=$S3_REGION --progress --acl-public sync output_prod/ s3://$S3_BUCKET 
