#!/usr/bin/env bash
set -uo pipefail
export HOME=/chroot/home/professo
export PATH="/chroot/home/professo/.local/bin:/opt/remi/php82/root/usr/bin:/usr/local/bin:/usr/bin:/bin"
PROJECT=/chroot/home/professo/professorpeptides.co/html
LOCK=/tmp/pp-dev-processor.lock
LOG="$PROJECT/storage/logs/dev-processor.log"
PHP=/opt/remi/php82/root/usr/bin/php
CLAUDE=/chroot/home/professo/.local/bin/claude
cd "$PROJECT" || exit 1
exec 9>"$LOCK"; flock -n 9 || exit 0
PENDING=$("$PHP" artisan devrequests:count 2>/dev/null | tail -1 | tr -d '[:space:]')
case "$PENDING" in ''|*[!0-9]*) PENDING=0 ;; esac
[ "$PENDING" -gt 0 ] || exit 0
echo "[$(date '+%F %T')] tick: $PENDING pending" >> "$LOG"
"$CLAUDE" --permission-mode bypassPermissions -p "$(cat .claude/process-dev-requests.md)" >> "$LOG" 2>&1
echo "[$(date '+%F %T')] tick: done" >> "$LOG"
