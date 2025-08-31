#!/usr/bin/env bash
set -euo pipefail
B="http://127.0.0.1:8195"
CK="/tmp/koi.cookies"

# cookie + login (usuario interno)
curl -s -c "$CK" "$B/master.php" >/dev/null
curl -s -b "$CK" -c "$CK" -d 'user=jony&pass=Route667&empresa=1' "$B/master.php" >/dev/null

for page in "master.php" "master.php?pagename=index"; do
  echo "=== $page ==="
  curl -s -b "$CK" "$B/$page" \
  | grep -oE '(src|href)=["'\'']/[^"'\'' ]+\.(js|css)' \
  | sed -E 's/^(src|href)=["'\''](.*)$/\2/' \
  | sort -u \
  | while read -r u; do
      printf "%-60s -> " "$u"
      curl -sI -b "$CK" "$B$u" | awk "/^HTTP/{print \$2}"
    done
done
