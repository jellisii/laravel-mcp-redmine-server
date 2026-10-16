#!/bin/sh
set -e

ENV_FILE="/var/www/mcp-test/.env"

# ── APP_KEY ──────────────────────────────────────────────────────────────────
APP_KEY_CURRENT="${APP_KEY:-}"
if [ -z "${APP_KEY_CURRENT}" ] || [ "${APP_KEY_CURRENT}" = "base64:" ] || [ "${#APP_KEY_CURRENT}" -lt 10 ]; then
    echo "[entrypoint] APP_KEY is empty or invalid — generating..."
    php artisan key:generate --force --no-interaction
    APP_KEY_CURRENT=$(grep '^APP_KEY=' "$ENV_FILE" | cut -d= -f2-)
    export APP_KEY="${APP_KEY_CURRENT}"
    echo "[entrypoint] APP_KEY generated: ${APP_KEY_CURRENT}"
fi

# ── MCP_AUTH_TOKEN ──────────────────────────────────────────────────────────
MCP_TOKEN="${MCP_AUTH_TOKEN:-}"
if [ -z "${MCP_TOKEN}" ] || [ "${#MCP_TOKEN}" -lt 64 ]; then
    TOKEN=$(openssl rand -hex 32)
    # Update .env inside container
    if grep -q '^MCP_AUTH_TOKEN=' "$ENV_FILE" 2>/dev/null; then
        sed -i "s|^MCP_AUTH_TOKEN=.*|MCP_AUTH_TOKEN=${TOKEN}|" "$ENV_FILE"
    else
        echo "MCP_AUTH_TOKEN=${TOKEN}" >> "$ENV_FILE"
    fi
    export MCP_AUTH_TOKEN="${TOKEN}"
    echo ""
    echo "══════════════════════════════════════════════════════════════"
    echo "  MCP_AUTH_TOKEN: ${TOKEN}"
    echo "══════════════════════════════════════════════════════════════"
    echo "  COPY this token to your MCP client config."
    echo "  Show again: docker-compose exec mcp cat .env | grep MCP_AUTH_TOKEN"
    echo "══════════════════════════════════════════════════════════════"
    echo ""
fi

exec "$@"
