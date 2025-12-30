#!/usr/bin/env bash
set -euo pipefail

PORT="${PORT:-8000}"

if ! command -v cloudflared >/dev/null 2>&1; then
  echo "cloudflared is required for tunneling. Install it from https://developers.cloudflare.com/cloudflare-one/connections/connect-apps/install-and-setup/installation/." >&2
  echo "After installing, rerun this script or run: cloudflared tunnel --url http://localhost:${PORT}" >&2
  exit 1
fi

php -S 0.0.0.0:"${PORT}" > /tmp/php-server.log 2>&1 &
PHP_PID=$!
cleanup() {
  kill "${PHP_PID}" >/dev/null 2>&1 || true
}
trap cleanup EXIT INT TERM

echo "Starting PHP server on port ${PORT} (logs: /tmp/php-server.log)" >&2
echo "Launching cloudflared tunnel..." >&2
cloudflared tunnel --url "http://localhost:${PORT}"
