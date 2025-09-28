#!/bin/bash

# Go to your repo directory (optional)
# cd /path/to/your/repo

# Check for changes
if [[ -n $(git status --porcelain) ]]; then
    echo "Changes detected."

    # Prompt for commit message
    read -rp "Enter commit message: " COMMIT_MSG

    # Make sure user entered something
    if [[ -z "$COMMIT_MSG" ]]; then
        echo "Error: Commit message cannot be empty."
        exit 1
    fi

    # Add all changes
    git add .

    # Commit with user message
    git commit -m "$COMMIT_MSG"

    # Push to current branch
    BRANCH=$(git branch --show-current)
    git push origin "$BRANCH"

    echo "Changes pushed successfully."
else
    echo "No changes to commit."
fi

