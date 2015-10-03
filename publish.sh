#!/bin/bash

# Replace "sculpin generate" with "php sculpin.phar generate" if sculpin.phar
# was downloaded and placed in this directory instead of sculpin having been
# installed globally.

sculpin generate --env=prod
if [ $? -ne 0 ]; then echo "Could not generate the site"; exit 1; fi

# Add --delete right before "output_prod" to have rsync remove files that are
# deleted locally from the destination too. See README.md for an example.
rsync -avze 'ssh -p 4668' output_prod/ username@yoursculpinsite:public_html
if [ $? -ne 0 ]; then echo "Could not publish the site"; exit 1; fi
