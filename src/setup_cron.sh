#!/bin/bash
# This script sets up a CRON job to run cron.php every 24 hours.

# Absolute path to PHP binary (adjust this if needed)
PHP_BIN=$(which php)

# Absolute path to cron.php
SCRIPT_PATH="$(cd "$(dirname "$0")"; pwd)/cron.php"

# CRON expression: every day at midnight
CRON_EXPRESSION="0 0 * * * $PHP_BIN $SCRIPT_PATH"

# Check if the CRON job already exists
crontab -l 2>/dev/null | grep -F "$SCRIPT_PATH" > /dev/null

if [ $? -eq 0 ]; then
    echo "✅ CRON job already exists."
else
    # Add the CRON job
    (crontab -l 2>/dev/null; echo "$CRON_EXPRESSION") | crontab -
    echo "✅ CRON job added to run cron.php every 24 hours."
fi
