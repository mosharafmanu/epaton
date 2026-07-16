#!/bin/bash
#
# Deploy theme to live server via SFTP using lftp mirror
# Usage: ./deploy.sh [--dry-run]

REMOTE_HOST="epaton"
REMOTE_PATH="/home/epaton/public_html/wp-content/themes/epaton"
LOCAL_PATH="$(cd "$(dirname "$0")" && pwd)"

EXCLUDE=(
	".git/"
	".gitignore"
	"node_modules/"
	"vendor/"
	"composer.lock"
	"package-lock.json"
	"yarn.lock"
	"npm-debug.log*"
	".DS_Store"
	"*.map"
	"deploy.sh"
)

EXCLUDE_OPTS=""
for pattern in "${EXCLUDE[@]}"; do
	EXCLUDE_OPTS="$EXCLUDE_OPTS -x \"$pattern\""
done

if [ "$1" = "--dry-run" ]; then
	echo "=== DRY RUN ==="
	lftp -e "set sftp:auto-confirm yes; mirror --verbose --dry-run $EXCLUDE_OPTS -R \"$LOCAL_PATH/\" \"$REMOTE_PATH/\"" sftp://$REMOTE_HOST
else
	echo "=== DEPLOYING ==="
	lftp -e "set sftp:auto-confirm yes; mirror --verbose $EXCLUDE_OPTS -R \"$LOCAL_PATH/\" \"$REMOTE_PATH/\"; quit" sftp://$REMOTE_HOST
	echo "=== DEPLOY COMPLETE ==="
fi
