#!/usr/bin/env bash
set -euo pipefail

PORT="${PORT:-8000}"
DOC_ROOT="${DOC_ROOT:-.}"

echo "Starting PHP server on http://0.0.0.0:${PORT} (doc root: ${DOC_ROOT})" >&2
php -S 0.0.0.0:"${PORT}" -t "${DOC_ROOT}"
