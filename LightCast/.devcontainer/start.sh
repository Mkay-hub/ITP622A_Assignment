#!/bin/bash
# This script starts the built-in PHP development server on port 8000

echo "Starting LightCast PHP server on port 8000..."

# The -S option starts a local web server.
# The -t option tells PHP where your main site files are (the /workspace folder).
php -S 0.0.0.0:8000 -t /workspace &

# Keeps the container running forever so it doesn’t shut down
sleep infinity
