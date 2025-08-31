#!/usr/bin/env bash
set -euo pipefail
B="http://127.0.0.1:8195"; CK="/tmp/koi_smoke.cookies"
echo "[1] Cookie"; curl -s -c "$CK" "$B/master.php" >/dev/null
echo "[2] Login jony"; curl -s -b "$CK" -c "$CK" -d 'user=jony&pass=Route667&empresa=1' "$B/master.php" >/dev/null
echo "[3] whoami"; curl -s -b "$CK" "$B/tools/whoami_fw.php"
echo -e "\n[4] Home sin pagename"; curl -s -b "$CK" "$B/master.php" | head -n 30
echo -e "\n[5] pagename=index"; curl -s -b "$CK" "$B/master.php?pagename=index" | head -n 10
