#!/bin/bash

# Go to your project directory (adjust as needed)
cd /var/www/canadian-gamer.com/html

echo "📥 Pulling latest code from GitHub..."
git pull origin main

# Get the short commit hash
VERSION=$(git rev-parse --short HEAD)
echo "🔧 Updating CSS version to $VERSION"

# Replace version in your HTML file(s)
find . -name "*.html" -exec sed -i "s/style\.css?v=[^\"']*/style.css?v=$VERSION/g" {} \;

echo "✅ Code updated and version bumped."

# Optional: restart Nginx if needed
# sudo systemctl reload nginx

echo "🚀 Done."
