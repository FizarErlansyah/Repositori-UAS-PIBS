#!/usr/bin/env bash

# Usage: ./tools/setup-origin-fork.sh git@github.com:YOUR_USERNAME/Repositori-UAS-PIBS.git

FORK_URL=$1
if [ -z "$FORK_URL" ]; then
  echo "Usage: $0 <fork-remote-url>"
  exit 1
fi

cd "$(dirname "$0")/.." || exit 1

# Rename current origin to upstream
if git remote get-url origin >/dev/null 2>&1; then
  git remote rename origin upstream
  echo "Renamed 'origin' to 'upstream'"
fi

# Add fork as origin
git remote add origin "$FORK_URL"

# Push current branch to origin
BRANCH=$(git rev-parse --abbrev-ref HEAD)

git push -u origin "$BRANCH"

echo "Origin set to $FORK_URL and branch $BRANCH pushed"
