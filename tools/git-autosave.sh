#!/usr/bin/env bash

# git-autosave.sh
# Watches a directory and auto-commits+pushes to origin/main
# Usage: ./tools/git-autosave.sh [path-to-watch] ["commit message"]

WATCH_DIR=${1:-.}
COMMIT_MSG=${2:-"Auto-commit: save from git-autosave"}

# Check for fswatch
if ! command -v fswatch >/dev/null 2>&1; then
  echo "fswatch command not found. Install via: brew install fswatch" >&2
  exit 1
fi

cd "$WATCH_DIR" || exit 1

# Ensure we are inside a git repo
if [ ! -d .git ]; then
  echo "No .git directory: this script must be run in a git repository (or set WATCH_DIR to one)." >&2
  exit 1
fi

echo "Starting git-autosave in $(pwd). Press Ctrl-C to stop."

while true; do
  # Wait for a change
  fswatch -1 -r --exclude "/.git/" .
  # Stage changes
  git add -A
  # Commit if there are staged changes
  if ! git diff --cached --quiet; then
    git commit -m "$COMMIT_MSG" || true
    # Try to push
    git push origin HEAD || true
  fi
  sleep 1
done
