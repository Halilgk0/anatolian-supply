#!/bin/sh
set -e

# A Postgres database connected through the Vercel Marketplace (Neon) arrives
# as DATABASE_URL. Point Laravel at it, and keep the cache (used for the
# inquiry form's rate limit) there too so it is shared between instances.
if [ -n "${DATABASE_URL:-}" ] && [ -z "${DB_URL:-}" ]; then
    export DB_CONNECTION=pgsql
    export DB_URL="$DATABASE_URL"

    if [ "${CACHE_STORE:-array}" = "array" ]; then
        export CACHE_STORE=database
    fi
fi

if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    # No managed database yet: serve the demo catalogue baked into the image.
    # Each instance gets its own copy, so changes made in the admin do not last.
    SEED_DB="/app/database/seeded.sqlite"
    LIVE_DB="${DB_DATABASE:-/tmp/database.sqlite}"

    if [ -f "$SEED_DB" ] && [ ! -f "$LIVE_DB" ]; then
        cp "$SEED_DB" "$LIVE_DB"
    fi
fi

# Create missing tables and, in an empty store, the demo products. This runs in
# the background so the server starts answering immediately; once the schema is
# current it finishes in well under a second.
php artisan magaza:hazirla --no-interaction >&2 &

exec "$@"
