#!/usr/bin/env bash

sh -c "! (find bin/ -type f -name \"*.php\" $1 -exec php -l -n {} \; | grep -v \"No syntax errors detected\")"
sh -c "! (find src/ -type f -name \"*.php\" $1 -exec php -l -n {} \; | grep -v \"No syntax errors detected\")"
