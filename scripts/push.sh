#!/bin/bash

# Go to your repo directory (optional)
# cd /path/to/your/repo

# Check for changes
if [[ -n $(git status --porcelain) ]]; then
    echo "Changes detected. Adding, committing, and pushing..."

    # Add all changes
    git add .

    # Commit with timestamp
    TIMESTAMP=$(date +"%Y-%m-%d %H:%M:%S")
    git commit -m "Auto-commit: $TIMESTAMP"

    # Push to current branch
    git push
else
    echo "No changes to commit."
fi
