#!/usr/bin/env bash
set -u
B="http://127.0.0.1:8195"
CK="/tmp/koi.cookies"

# 1) cookie + login
curl -s -c "$CK" "$B/master.php" >/dev/null
curl -s -b "$CK" -c "$CK" -d 'user=jony&pass=Route667&empresa=1' "$B/master.php" >/dev/null

# 2) capturar el HTML de home
TMP=$(mktemp)
curl -s -b "$CK" "$B/master.php" >"$TMP"

# 3) extraer assets referenciados
assets=$(grep -oE '(src|href)=["'\'']/?[^"'\'' ]+\.(js|css)' "$TMP" \
         | sed -E 's/^(src|href)=["'\'']//' | sort -u)

# 4) verificar cada asset con timeout y latencia
echo "$assets" | awk '{print NR, $0}' | while read -r n u; do
  url="$u"
  [[ "$url" != /* ]] && url="/$url"
  printf "%3d) %-70s -> " "$n" "$url"
  curl -sI -b "$CK" --connect-timeout 2 --max-time 5 "$B$url" \
       -w "%{http_code} %{time_total}s\n" -o /dev/null || echo "ERR(timeout)"
done

rm -f "$TMP"
