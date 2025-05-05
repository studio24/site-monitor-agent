#!/usr/bin/env bash

####################################################################
# Install WordPress files so we can test the WordPress collector
#
# Usage:
# install-wordpress.sh path/to/folder
####################################################################

# Get destination folder to install WP to from argument
if [ $# -gt 0 ]; then
    TMPDIR=$1
else
    echo "You must pass the temp folder path where we can install files to"
    exit 1
fi

# Reset folder
WP_CORE_DIR=$TMPDIR
WP_PLUGIN_DIR=$WP_CORE_DIR/wp-content/plugins
rm -Rf $WP_CORE_DIR
mkdir -p $WP_CORE_DIR

# Download WP
echo "Downloading WordPress to $WP_CORE_DIR"
wget -q https://wordpress.org/latest.tar.gz -O $TMPDIR/wordpress.tar.gz
tar --strip-components=1 -zxmf $TMPDIR/wordpress.tar.gz -C $WP_CORE_DIR
rm $TMPDIR/wordpress.tar.gz

# Add any plugins we want to install
declare -a plugins=(
    "advanced-custom-fields"
    "classic-editor"
    "wordpress-seo"
)

# Loop through all plugins and download the latest version
for plugin in "${plugins[@]}"; do
    echo "Downloading plugin $plugin"
    wget -q https://downloads.wordpress.org/plugin/$plugin.latest-stable.zip -O $WP_PLUGIN_DIR/$plugin.latest-stable.zip
    unzip -qu $WP_PLUGIN_DIR/$plugin.latest-stable.zip -d $WP_PLUGIN_DIR
    rm $WP_PLUGIN_DIR/$plugin.latest-stable.zip
done

echo "All done!"
exit 0