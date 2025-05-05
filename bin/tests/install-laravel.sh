#!/usr/bin/env bash

####################################################################
# Install Laravel files so we can test the Laravel collector
#
# Usage:
# install-laravel.sh path/to/folder
####################################################################

# Get destination folder to install WP to from argument
if [ $# -gt 0 ]; then
    TMPDIR=$1
else
    echo "You must pass the temp folder path where we can install files to"
    exit 1
fi

# Reset folder
LARAVEL_CORE_DIR=$TMPDIR
rm -Rf $LARAVEL_CORE_DIR
mkdir -p $LARAVEL_CORE_DIR

# We need to do this on PHP 5.6 to ensure Laravel installs correctly (otherwise plugin kylekatarnls/update-helper halts composer install)
composer --global config allow-plugins false

# Download Laravel
echo "Downloading Laravel to $LARAVEL_CORE_DIR"
rm -Rf $LARAVEL_CORE_DIR
composer --no-dev -q --no-scripts create-project laravel/laravel $LARAVEL_CORE_DIR

echo "All done!"
exit 0
