#!/usr/bin/env bash

####################################################################
# Install Composer packages so we can test the Composer collector
#
# Usage:
# install-composer.sh path/to/folder
####################################################################

# Get destination folder to install WP to from argument
if [ $# -gt 0 ]; then
    TMPDIR=$1
else
    echo "You must pass the temp folder path where we can install files to"
    exit 1
fi

# Reset folder
CORE_DIR=$TMPDIR
rm -Rf $CORE_DIR
mkdir -p $CORE_DIR
mkdir -p $CORE_DIR/folder

# Copy composer.json
cat bin/tests/composer/composer.json > $CORE_DIR/composer.json
cat bin/tests/composer/composer2.json > $CORE_DIR/folder/composer.json

# Download packages
cd $CORE_DIR
composer --no-dev -q install
cd folder
composer --no-dev -q install

echo "All done!"
exit 0
